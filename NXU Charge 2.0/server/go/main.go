package main

import (
	"database/sql"
	"fmt"
	"net/http"
	"time"
	"io/ioutil"
	"encoding/json"
	"compress/gzip"
	"strings"
	"strconv"

	_ "github.com/go-sql-driver/mysql"
)

var db *sql.DB

var dataMap = map[string][]string{
	"c14":   []string{"88227178", "88227167", "88227166", "88227164", "89627130", "89627062", "89627114", "89627112", "89627111", "89624194", "89627126", "89627113"},
	"a2":    []string{"88227927", "88227928", "88227165"},
	"acar":  []string{"88227163", "89626666"},
	"b7":    []string{"88227942", "89623528"},
	"b10":   []string{"88227929", "88227943"},
	"c8":    []string{"86060206", "86060232", "86062777", "86062778", "86062829", "86060241", "86060202", "86062776", "86060208", "86060236"},
	"c7":    []string{"88232072", "88232071", "88232178", "88232176"},
	"c12":   []string{"88232173", "88232070", "88232177", "88232179"},
	"c13":   []string{"88232174", "88232175", "88232073", "88232172"},
	"c8-1":  []string{"88230806", "88230807", "88230461", "88230805", "88230704", "88230810", "88230812", "88230815", "88230816", "88230686"},
}

var dataArray = map[string]string{
	"88232072": "c7",
	"88232071": "c7",
	"88232178": "c7",
	"88232176": "c7",
	
	"88232173": "c12",
	"88232070": "c12",
	"88232177": "c12",
	"88232179": "c12",
	
	"88232174": "c13",
	"88232175": "c13",
	"88232073": "c13",
	"88232172": "c13",
	
	"88230806": "c8-1",
	"88230807": "c8-1",
	"88230461": "c8-1",
	"88230805": "c8-1",
	"88230704": "c8-1",
	"88230810": "c8-1",
	"88230812": "c8-1",
	"88230815": "c8-1",
	"88230816": "c8-1",
	"88230686": "c8-1",
	
	"88227178": "c14",
	"88227167": "c14",
	"88227166": "c14",
	"88227164": "c14",
	"89627130": "c14",
	"89627062": "c14",
	"89627114": "c14",
	"89627112": "c14",
	"89627111": "c14",
	"89624194": "c14",
	"89627126": "c14",
	"89627113": "c14",
	
	"88227927": "a2",
	"88227928": "a2",
	"88227165": "a2",
	
	"88227163": "acar",
	"89626666": "acar",
	
	"88227942": "b7",
	"89623528": "b7",
	
	"88227929": "b10",
	"88227943": "b10",
	
	"86060206": "c8",
	"86060232": "c8",
	"86062777": "c8",
	"86062778": "c8",
	"86062829": "c8",
	"86060241": "c8",
	"86060202": "c8",
	"86062776": "c8",
	"86060208": "c8",
	"86060236": "c8",
}

var timeLocation *time.Location
var timeError error

func main() {
	timeLocation, timeError = time.LoadLocation("Asia/Shanghai")
	if timeError != nil {
        fmt.Println("无法加载指定的时区。")
        return
    }
	// 设置默认时区为"Asia/Shanghai"
	time.Local = timeLocation

	fmt.Println("连接数据库...")
	// MySQL连接信息
	servername := "root:123456@tcp(127.0.0.1:3306)/test"

	// 连接MySQL数据库
	dbConn, err := connectWithRetry(servername)
	if err != nil {
		fmt.Println("Database connection failed after retries:", err)
		return
	}

	fmt.Println("数据库连接成功！")

	// 将数据库连接赋值给全局变量
	db = dbConn

	fmt.Println("启动服务器...")
	http.HandleFunc("/", handlerFunc)

	// 启动服务器并监听端口8888
	serverErr := http.ListenAndServe("127.0.0.1:2254", nil)
	if serverErr != nil {
		fmt.Println("服务器启动失败:", err)
		return
	}
}

func handlerFunc(w http.ResponseWriter, r *http.Request) {
	ip := r.RemoteAddr
	fmt.Printf("\n访问IP：%s\n访问路径：%s", ip, r.URL.Path)
	// fmt.Printf("访问路径：%s", r.URL.Path)
	switch r.URL.Path {
	case "/update-data":
		updateData(w, r);
	case "/get-data":
		getData(w, r);
	default:
		http.NotFound(w, r)
	}
}

func updateData(w http.ResponseWriter, r *http.Request) {
	var using bool
	using_err := db.QueryRow("SELECT `using` FROM data WHERE id = 1").Scan(&using)
	if using_err != nil {
		fmt.Println("Error fetching initial using:", using_err)
		return
	}
	if (using) {
		fmt.Println("正在更新中")
		return
	} else {
		// 执行查询
		update_using, update_using_err := db.Exec("UPDATE `data` SET `using` = TRUE WHERE id = 1")
		if update_using_err != nil {
			fmt.Printf("更新数据库失败：%v\n",  update_using_err)
			return
		}
		n, update_using_n_err := update_using.RowsAffected() // 操作影响的行数
		if update_using_n_err != nil {
			fmt.Printf("get RowsAffected failed, err:%v\n", update_using_n_err)
			return
		}
		fmt.Printf("设置为正在更新, affected rows:%d\n", n)
	}
	seepower_pid_start := fetchInitialNum()
	if seepower_pid_start == -1 {
		fmt.Println("Error fetching initial num from database")
		fmt.Fprintln(w, `{"code": 502, "success": false, "msg": "seepower_pid获取失败"}`)
		return
	}

	seepower_pid := seepower_pid_start

	finish_loop := false

	for !finish_loop {
		if (seepower_pid - seepower_pid_start > 25000) {
			break
		}
		retries := 0
		for retries < 3 {
			url := fmt.Sprintf("https://h5.2ye.cn/api/chargerlog/power?seepower_pid=%d", seepower_pid)

			// fmt.Println(string(url))

			payload := strings.NewReader("")

			req, err := http.NewRequest("POST", url, payload)
			if err != nil {
				fmt.Println("Error creating request:", err)
				return
			}

			req.Header.Add("Host", "h5.2ye.cn")
			req.Header.Add("Connection", "keep-alive")
			req.Header.Add("Content-Length", "0")
			req.Header.Add("tls", fmt.Sprintf("%d", time.Now().UnixNano()/int64(time.Millisecond)))
			req.Header.Add("Accept", "application/json, text/plain, */*")
			req.Header.Add("clientid", "your clientid") //自行从官方接口爬取 clientid
			req.Header.Add("User-Agent", "Mozilla/5.0 (Linux; Android 12; ELS-AN00 Build/HUAWEIELS-AN00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/86.0.4240.99 XWEB/4435 MMWEBSDK/20230202 Mobile Safari/537.36 MMWEBID/9699 MicroMessenger/8.0.33.2320(0x28002151) WeChat/arm64 Weixin NetType/WIFI Language/zh_CN ABI/arm64")
			req.Header.Add("Origin", "https://h5.2ye.cn")
			req.Header.Add("X-Requested-With", "com.tencent.mm")
			req.Header.Add("Sec-Fetch-Site", "same-origin")
			req.Header.Add("Sec-Fetch-Mode", "cors")
			req.Header.Add("Sec-Fetch-Dest", "empty")
			req.Header.Add("Referer", "https://h5.2ye.cn/")
			req.Header.Add("Accept-Encoding", "gzip, deflate")
			req.Header.Add("Accept-Language", "zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7")

			client := &http.Client{
				Timeout: 500 * time.Millisecond,
			}
			resp, err := client.Do(req)
			if err != nil {
				fmt.Println("Error making request:", err)
				retries++
				break
			}
			defer resp.Body.Close()

			var bodyBytes []byte

			// Check if the response is compressed (gzip)
			if strings.Contains(resp.Header.Get("Content-Encoding"), "gzip") {
				reader, err := gzip.NewReader(resp.Body)
				if err != nil {
					fmt.Println("Error creating gzip reader:", err)
					return
				}
				defer reader.Close()

				bodyBytes, err = ioutil.ReadAll(reader)
				if err != nil {
					fmt.Println("Error reading gzipped response body:", err)
					return
				}
			} else {
				// If not compressed, read the response as is
				bodyBytes, err = ioutil.ReadAll(resp.Body)
				if err != nil {
					fmt.Println("Error reading response body:", err)
					return
				}
			}

			// fmt.Println(string(bodyBytes))

			var data map[string]interface{}
			err = json.Unmarshal(bodyBytes, &data)
			if err != nil {
				fmt.Println("Error decoding JSON:", err)
				// fmt.Fprintln(w, `{"code": 502, "success": false, "msg": "数据解析失败"}`)
				return
			}

			if errCode, ok := data["err_code"].(float64); ok {
				if errMsg, ok := data["err_msg"].(string); ok && errCode == 502 && errMsg == "记录不存在" {
					fmt.Println("Record not found. Exiting loop.")
					finish_loop = true
					break;
				}
			}

			productId, ok := data["data"].(map[string]interface{})["productid"].(string)
			if !ok {
				fmt.Println("productid 不存在或不是字符串类型")
				return
			}

			port, ok := data["data"].(map[string]interface{})["port"].(float64)
			if !ok {
				fmt.Println("port 不存在或不是字符串类型")
				return
			}
			port_string := strconv.FormatFloat(port, 'f', 0, 64)

			start_time, ok := data["data"].(map[string]interface{})["start_date"].(string)
			if !ok {
				fmt.Println("startTime 不存在或不是字符串类型")
				return
			}

			total_time, ok := data["data"].(map[string]interface{})["total_time"].(float64)
			if !ok {
				fmt.Printf("totalTime 不存在或不是字符串类型 %T", data["data"].(map[string]interface{})["total_time"])
				return
			}

			// total_time_minutes, err := strconv.Atoi(total_time)
			// if err != nil {
			// 	fmt.Println("分钟数转换失败:", err)
			// 	return
			// }

			timeObj, err := time.ParseInLocation("2006-01-02 15:04", start_time, timeLocation)
			if err != nil {
				fmt.Println("时间解析错误:", err)
				return
			}

			// 将时间对象转换为毫秒级时间戳
			// milliseconds := timeObj.UnixNano() / int64(time.Millisecond)

			// 加上 b 分钟
			timePlusBMinutes := timeObj.Add(time.Duration(total_time) * time.Minute)

			// 将加上分钟数后的时间对象转换为毫秒级时间戳
			millisecondsWithBMinutes := timePlusBMinutes.UnixNano() / int64(time.Millisecond)

			value, exists := dataArray[productId]
			if exists {
				// fmt.Printf("%s  %s  %s  %d\n", value, port_string, productId, millisecondsWithBMinutes)
				// 构建更新语句
				// 构建 UPDATE 查询
				query := fmt.Sprintf("UPDATE `%s` SET `%s` = ? WHERE id = %s", value, port_string, productId)

				// 执行查询
				update, err := db.Exec(query, millisecondsWithBMinutes)
				if err != nil {
					fmt.Printf("更新数据库失败：%v\n", err)
					return
				}
				n, err := update.RowsAffected() // 操作影响的行数
				if err != nil {
					fmt.Printf("get RowsAffected failed, err:%v\n", err)
					return
				}
				fmt.Printf("%s  %s  %s  %d\n", value, port_string, productId, millisecondsWithBMinutes)
				fmt.Printf("数据库更新成功, affected rows:%d\n", n)
			} else {
				// fmt.Println("无效", string(productId), retries, seepower_pid)
				seepower_pid++
				break
			}

			// Increment seepower_pid
			seepower_pid++

			// Add a delay between API requests to avoid rate limiting or flooding the server
			// time.Sleep(1 * time.Second)
			break
		}

		if retries == 3 {
			fmt.Printf("seepower_pid %d - Failed\n", seepower_pid)
			seepower_pid++ // 继续下一个
		}
	}

	query := "UPDATE `data` SET num = ?, `timestamp` = ? WHERE id = 1"
	// 执行查询
	_, err := db.Exec(query, seepower_pid - 1, time.Now().UnixNano()/int64(time.Millisecond))
	if err != nil {
		fmt.Printf("更新数据库失败：%v\n", err)
		return
	}
	// 执行查询
	end_using, end_using_err := db.Exec("UPDATE `data` SET `using` = FALSE WHERE id = 1")
	if end_using_err != nil {
		fmt.Printf("更新数据库失败：%v\n",  end_using_err)
		return
	}
	n, end_using_n_err := end_using.RowsAffected() // 操作影响的行数
	if end_using_n_err != nil {
		fmt.Printf("get RowsAffected failed, err:%v\n", end_using_n_err)
		return
	}
	fmt.Printf("设置为结束更新, affected rows:%d\n", n)

	fmt.Println("数据库更新成功,结束", time.Now().UnixNano()/int64(time.Millisecond))
}


func connectWithRetry(servername string) (*sql.DB, error) {
	retries := 0
	for retries < 3 {
		db_try, err := sql.Open("mysql", servername)
		if err != nil {
			retries++
			fmt.Printf("连接数据库 - Retry %d - Error connecting to MySQL: %s\n", retries, err)
			continue
		}

		// 设置最大连接数和空闲连接数
		db_try.SetMaxOpenConns(25)
		db_try.SetMaxIdleConns(25)

		err = db_try.Ping()
		if err != nil {
			retries++
			fmt.Printf("连接数据库 - Retry %d - Error pinging database: %s\n", retries, err)
			db_try.Close()
			continue
		}

		return db_try, nil
	}

	return nil, fmt.Errorf("数据库连接失败（重试次数上限）")
}

func fetchInitialNum() int {
	var num int
	err := db.QueryRow("SELECT num FROM data WHERE id = ?", 1).Scan(&num)
	if err != nil {
		fmt.Println("Error fetching initial num:", err)
		return -1
	}
	fmt.Println("Initial num:", num)
	return num
}

func getData(w http.ResponseWriter, r *http.Request) {
	// 允许跨源请求的域名列表
	allowedOrigins := []string{
		"http://localhost:5173",
	}

	// 获取请求头中的 Origin
	origin := r.Header.Get("Origin")
	origin_continue := false

	// 检查 Origin 是否在允许的域名列表中
	// 如果 Origin 存在于允许列表中，则设置对应的 CORS 头部
	for _, allowedOrigin := range allowedOrigins {
		if allowedOrigin == origin {
			w.Header().Set("Access-Control-Allow-Origin", origin)
			origin_continue = true
			break
		}
	}

	if (!origin_continue) {
		return;
	}

	query := r.URL.Query()
	pile := query.Get("pile")
	locate := dataMap[pile]

	sqlStr := fmt.Sprintf("select * from `%s` where id > 0", pile)
	rows, err := db.Query(sqlStr)
	if err != nil {
		fmt.Printf("query failed, err:%v\n", err)
		return
	}
	// 非常重要：关闭rows释放持有的数据库链接
	defer rows.Close()

	pile_data := make(map[string]interface{})

	for _, value := range locate {
		pile_data[value] = []string{}
	}

	// 循环读取结果集中的数据
	for rows.Next() {
		var id string
		var value1, value2, value3, value4, value5, value6, value7, value8, value9, value10 string
		err := rows.Scan(&id, &value1, &value2, &value3, &value4, &value5, &value6, &value7, &value8, &value9, &value10)
		if err != nil {
			fmt.Println("Error scanning rows: ", err)
			continue
		}

		// 将1到4的数据拼接成字符串切片
		dataSlice := []string{value1, value2, value3, value4, value5, value6, value7, value8, value9, value10}

		// 将id和数据切片映射到dataMap中
		pile_data[id] = dataSlice
	}
	sqlStr = "select timestamp from data where id=1"
	var timestamp string
	// 非常重要：确保QueryRow之后调用Scan方法，否则持有的数据库链接不会被释放
	err = db.QueryRow(sqlStr).Scan(&timestamp)
	if err != nil {
		fmt.Printf("scan failed, err:%v\n", err)
		return
	}

	result := make(map[string]interface{})
	result["code"] = 200
	result["success"] = true
	result["data"] = pile_data
	result["timestamp"] = timestamp

	jsonString, err := json.Marshal(result)
	if err != nil {
		fmt.Println("JSON marshaling failed:", err)
		return
	}
	fmt.Fprintln(w, string(jsonString))
}