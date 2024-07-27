import 'mdui/mdui.css';
import 'mdui';

import { setColorScheme } from 'mdui/functions/setColorScheme.js';
import { setTheme } from 'mdui/functions/setTheme.js';
setColorScheme('#006874');

import '@mdui/icons/dark-mode.js';
import '@mdui/icons/light-mode.js';
import '@mdui/icons/dark-mode--outlined.js';
import '@mdui/icons/light-mode--outlined.js';

import { createApp, ref, watch, reactive } from 'vue'
import {
    ConfigProvider, 
    Popover,
    Icon,
    Skeleton,
    SkeletonTitle,
    SkeletonImage,
    SkeletonAvatar,
    SkeletonParagraph,
    PullRefresh,
    showConfirmDialog,
    CountDown,
    FloatingBubble,
    Loading,
    NoticeBar,
    Swipe,
    SwipeItem
} from 'vant';
import 'vant/lib/index.css';

import "@/assets/css/index.css";
import { VERSION, DATA_URL } from './public.js';


function lightToDark() {
    lightToDarkVar.value = !lightToDarkVar.value;
    if (lightToDarkVar.value) {
        lightToDarkContent.value = "light";
        setTheme('light');
        localStorage.setItem("theme", true);
    } else {
        lightToDarkContent.value = "dark";
        setTheme('dark');
        localStorage.setItem("theme", false);
    }
    error.value.isError = true;
}

function createXHR () {
    var XHR = [  //兼容不同浏览器和版本得创建函数数组
        function () { return new XMLHttpRequest () },
        function () { return new ActiveXObject ("Msxml2.XMLHTTP") },
        function () { return new ActiveXObject ("Msxml3.XMLHTTP") },
        function () { return new ActiveXObject ("Microsoft.XMLHTTP") }
    ];
    var xhr = null;
    //尝试调用函数，如果成功则返回XMLHttpRequest对象，否则继续尝试
    for (var i = 0; i < XHR.length; i ++) {
        try {
            xhr = XHR[i]();
        } catch(e) {
            continue  //如果发生异常，则继续下一个函数调用
        }
        break;  //如果成功，则中止循环
    }
    return xhr;  //返回对象实例
}

function getSecondsDifference(timestamp) {
    const currentTime = Date.now(); // 当前时间的时间戳（单位：秒）
    const inputTime = timestamp; // 输入时间的时间戳（单位：秒）
    const difference = inputTime - currentTime; // 计算时间差（单位：秒）
    return difference;
}

function getData() {
    // let result_array;
    // try {
    //     result_array = JSON.parse(`{"code":200,"succesful":true,"time":1722075424550,"token":true,"data":{"88227929":[1702374170000,1702374170000,1702374170000,0,1702374170000,1702374170000,1702374170000,1702374170000,0,1702374170000],"88227943":[1702374170000,1702374170000,1702374170000,1702374170000,1702374170000,1702374170000,1702374170000,1702374170000,1702374170000,1702374170000]}}`);
    //     if (result_array["code"] == 200 && result_array["succesful"] == true) {
    //         console.log(result_array);
    //         if (!result_array["token"]) {
    //             error.msg = "token";
    //             error.type = "non-refresh";
    //             error.isError = true;
    //         }
    //         dataProcessing(result_array);
    //     } else {
    //         error.msg = "data-custom";
    //         error.content = result_array["msg"];
    //         error.type = "normal";
    //         error.isError = true;
    //         pile_data_loading.value = false;
    //         return;
    //     }
    // } catch(e) {
    //     error.msg = "data";
    //     error.type = "normal";
    //     console.error("Error：" + e);
    //     error.isError = true;
    //     pile_data_loading.value = false;
    //     return;
    // }
    // return; // 本地调试用
    pile_data_loading.value = true;
    updateCountdownTime.value = 10000;
    var xhr = createXHR();
    let data_url = '/api/get-data';
    if (DATA_URL != '') {
        data_url = DATA_URL;
    }
    xhr.open("GET", data_url + '?pile=' + nowLocate.value);
    xhr.send();
    xhr.onload = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                let result_array;
                try {
                    result_array = JSON.parse(xhr.responseText);
                    if (result_array["code"] == 200 && result_array["successful"] == true) {
                        console.log(result_array);
                        if (!result_array["token"]) {
                            error.msg = "token";
                            error.type = "non-refresh";
                            error.isError = true;
                        }
                        dataProcessing(result_array);
                        return;
                    } else {
                        error.msg = "data-custom";
                        error.content = result_array["msg"];
                        error.type = "normal";
                        error.isError = true;
                        pile_data_loading.value = false;
                        return;
                    }
                } catch(e) {
                    error.msg = "data";
                    error.type = "normal";
                    console.error("Error：" + e);
                    error.isError = true;
                    pile_data_loading.value = false;
                    return;
                }
            }
        }
        error.msg = "data";
        error.type = "normal";
        error.isError = true;
        pile_data_loading.value = false;
    }
}

function dataProcessing(data) {
    console.log(data)
    pile_array.value = pileArrayTotal[nowLocate.value];
    console.log(pile_array)
    var pile_result = [];
    try {
        for (let i = 0; i < pile_array.value.length; i++) {
            pile_result.push([pile_array.value[i],[],[]])
            for (let j = 0; j < 10; j++) {
                if (j < 5) {
                    pile_result[i][1].push(getSecondsDifference(data.data[pile_array.value[i]][j]));
                } else {
                    pile_result[i][2].push(getSecondsDifference(data.data[pile_array.value[i]][j]));
                }
            }
        }
    } catch(e) {
        console.error("Error：" + e);
        return;
    }
    const timestamp = parseInt(data.time); // 将字符串转换为数字
    const date = new Date(timestamp); // 使用 Date 对象转换时间戳
    // 获取各种时间信息并补零
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // 月份是从 0 开始的，所以需要加1
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    // 构建格式化后的时间字符串
    const formattedTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

    console.log(pile_result);
    successText.value = "刷新成功 " + formattedTime;
    pile_data.value = pile_result;
    pile_data_loading.value = false;
    loading.value = false;
}

const lightToDarkVar = ref(true);
const lightToDarkContent = ref("light");
if (!window.matchMedia || !window.matchMedia('(prefers-color-scheme: dark)') || !window.matchMedia('(prefers-color-scheme: dark)').addEventListener) {
    if (localStorage.getItem('isDark') == "true") {
        lightToDarkVar.value = false;
    } else {
        lightToDarkVar.value = true;
    }
} else {
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        lightToDarkVar.value = false;
    } else {
        lightToDarkVar.value = true;
    }
}
if (localStorage.getItem("theme") != null) {
    lightToDarkVar.value = localStorage.getItem("theme") == "true";
}
if (lightToDarkVar.value) {
    lightToDarkContent.value = "light";
    setTheme("light")
} else {
    lightToDarkContent.value = "dark";
    setTheme("dark")
}

const nowLocate = ref("c14");
if (localStorage.getItem("pile") != null) {
    nowLocate.value = localStorage.getItem("pile");
}
const backgroundArray = {
    "c14": ["文<br>萃<br>公<br>寓<br>14<br>号<br>楼", "休<br>闲<br>文<br>化<br>广<br>场"],
    "a2": ["兰<br>山<br>公<br>寓<br>2<br>号<br>楼", "兰<br>芝<br>苑<br>餐<br>厅"],
    "b10": ["十<br>号<br>公<br>寓", "九<br>号<br>公<br>寓"],
    "c8": ["文<br>萃<br>公<br>寓<br>9<br>号<br>楼", "文<br>萃<br>公<br>寓<br>8<br>号<br>楼"],
    "c7": ["文<br>萃<br>公<br>寓<br>6<br>号<br>楼", "文<br>萃<br>公<br>寓<br>7<br>号<br>楼"],
    "c8-1": ["文<br>萃<br>公<br>寓<br>9<br>号<br>楼", "文<br>萃<br>公<br>寓<br>8<br>号<br>楼"],
    "c12": ["文<br>萃<br>公<br>寓<br>7<br>号<br>楼", "文<br>萃<br>公<br>寓<br>12<br>号<br>楼"],
    "c13": ["文<br>萃<br>公<br>寓<br>12<br>号<br>楼", "文<br>萃<br>公<br>寓<br>13<br>号<br>楼"],
}
const pileArrayTotal = {
    "c14": ["88227178","88227167","88227166","88227164","89627130","89627062","89627114","89627112","89627111","89624194","89627126","89627113"],
    "a2": ["88227927","88227928","88227165"],
    "b10": ["88227929","88227943"],
    "c8": ["86060206","86060232","86062777","86062778","86062829","86060241","86060202","86062776","86060208","86060236"],
    "c7": ["88232072","88232071","88232178","88232176"],
    "c12": ["88232173","88232070","88232177","88232179"],
    "c13": ["88232174","88232175","88232073","88232172"],
    "c8-1": ["88230686", "88230816", "88230815", "88230812", "88230810", "88230704", "88230805", "88230461", "88230807", "88230806"]
};
const pile_array = ref(pileArrayTotal[nowLocate.value]);
const pile_data = ref([]);
// pile_data.value = [true, [["88227929",[1,1,1700544501333,0,0],[0,1,1,0,1700464927000]],["88227923",[1,1,1,0,0],[0,1,1,0,1]],["11111111",[1,1,1,0,0],[0,1,1,0,1]]]];

const background = ref([]);
background.value = backgroundArray[nowLocate.value];

const loading = ref(false);
const pile_data_loading = ref(true);
const successText = ref("刷新成功");
const updateCountdownTime = ref(10000);

const error = reactive({isError: false, msg: "", type: "normal"});
watch(error, (newError) => {
    if (!newError.isError) {
        return;
    }
    const error_array = {
        "data": ["数据出错", "获取数据发生错误\n若多次刷新仍出现该错误\n请与开发者联系"],
        "data-custom": ["数据出错", newError.content],
        "token": ["更新数据出现问题", "当前服务器使用的token已失效\n这会导致部分空闲充电桩错误显示为正在使用\n开发者会不定期检查token的可用性，但可能目前尚未发现该问题\n你可以反馈问题\n或者使用微信打开以下链接\nhttps://nxu-charge.thisish.cn/api/token/get-token\n或者忽略这个问题继续使用"],
    }
    if (newError.type == "normal") {
        showConfirmDialog({
            title: error_array[error.msg][0],
            message: error_array[error.msg][1],
            confirmButtonText: '刷新页面',
            cancelButtonText: '反馈问题',
            width: "80%",
            closeOnPopstate: false
        }).then(() => {
            location.reload();
        }).catch(() => {
            location.href = "https://support.qq.com/product/533385";
        });
    } else if (newError.type == "non-refresh") {
        showConfirmDialog({
            title: error_array[error.msg][0],
            message: error_array[error.msg][1],
            confirmButtonText: '继续',
            cancelButtonText: '反馈问题',
            width: "80%",
            closeOnPopstate: false
        }).then(() => {
        }).catch(() => {
            location.href = "https://support.qq.com/product/533385";
        });
    }
});

// nowLocate.value = "c8-1"; // 本地调试用
getData();

createApp({
    setup() {
        return {
            lightToDark,
            lightToDarkContent
        }
    }
}).mount('#light-to-dark-div');

const app = createApp({
    setup() {
        return {
            lightToDarkContent
        }
    }
});
app.use(ConfigProvider);
app.mount('#config-provider');

const app1 = createApp({
    setup() {
        const showPopover = ref(false);
        const container = ref({active: false})

        // 通过 actions 属性来定义菜单选项
        const actions = [
            { text: '关于我们' },
            { text: '常见问题' },
        ];
        const onSelect = (action) => {
            console.log(action.text)
            switch(action.text) {
                case "关于我们":
                    location.href = '/main/about';
                    break;
                case "常见问题":
                    location.href = '/main/qa';
                    break;
            }

        };
        watch(showPopover, (newShowPopover) => {
            if (newShowPopover) {
                container.value.active = true;
            } else {
                container.value.active = false;
            }
        });

        return {
            actions,
            onSelect,
            showPopover,
            lightToDarkContent,
            container
        };
    },
});
app1.use(Popover);
app1.mount("#about-popover");

const app2 = createApp({
    setup() {
        const showPopover = ref(false);
        const locateIcon = ref({
            transform: "rotate(0deg)",
            transition: "all 0.3s cubic-bezier(0.4, 0.0, 0.2, 1)"
        });

        const actions_total = [];
        const locate_array = {
            "a2": "A区2号公寓楼前充电桩",
            "b10": "B区10号楼充电桩",
            "c7": "C区7号楼下充电桩",
            "c8": "C区8号楼下充电桩(靠楼)",
            "c8-1": "C区8号楼下充电桩(靠路)",
            "c12": "C区12号楼下充电桩",
            "c13": "C区13号楼下充电桩",
            "c14": "C区14号楼前充电桩",
        };
        for (let key in locate_array) {
            actions_total.push({text: locate_array[key], num: key});
        }

        const locate = ref("");
        locate.value = locate_array[nowLocate.value];

        // 通过 actions 属性来定义菜单选项
        const actions = ref([]);
        actions.value = actions_total.filter(item => item.num !== nowLocate.value);
        const onSelect = (action) => {
            locate.value = action.text;
            nowLocate.value = action.num;
            actions.value = actions_total.filter(item => item.text !== action.text);
            window.localStorage.setItem("pile", action.num);
            background.value = backgroundArray[nowLocate.value];
            getData()
        };
        watch(showPopover, (newShowPopover) => {
            if (newShowPopover) {
                locateIcon.value.transform = "rotate(45deg)";
            } else {
                locateIcon.value.transform = "rotate(0deg)";
            }
        });

        return {
            actions,
            onSelect,
            showPopover,
            lightToDarkContent,
            locateIcon,
            locate,
        };
    },
});
app2.use(Popover);
app2.use(Icon);
app2.mount("#locate-popover");

createApp({
    setup() {
        return {
            VERSION
        }
    }
}).mount('#version');

const isTop = ref(true);
const app3 = createApp({
    setup() {
        const direction = ref("");
        const offset = ref({ y: 620 });
        // offset.value.x = window.innerWidth - 70;
        offset.value.y = window.innerHeight - 100;
        const onRefresh = () => {
            getData();
        };

        const bubbleOnClick = () => {
            if (direction.value != "reverse") {
                direction.value = "reverse";
            } else {
                direction.value = "";
            }
        };

        const onFinish = (element) => {
            document.querySelector(".port[value='" + element + "']").classList.add("free");
            document.querySelector(".port[value='" + element + "']").classList.remove("using");
            document.querySelector(".port[value='" + element + "']").classList.remove("tofree");
        };

        const updateFinish = () => {
            if (pile_data_loading.value == true) {
                error.msg = "data-custom";
                error.content = "数据加载超时\n请刷新重试\n若多次刷新仍出现该错误\n请与开发者联系";
                error.type = "normal";
                error.isError = true;
            }
        }

        return {
            loading,
            onRefresh,
            pile_data,
            isTop,
            getSecondsDifference,
            bubbleOnClick,
            direction,
            offset,
            backgroundArray,
            background,
            onFinish,
            successText,
            pile_data_loading,
            updateCountdownTime,
            updateFinish
        };
    },
});
app3.use(Skeleton);
app3.use(SkeletonTitle);
app3.use(SkeletonImage);
app3.use(SkeletonAvatar);
app3.use(SkeletonParagraph);
app3.use(PullRefresh);
app3.use(CountDown);
app3.use(FloatingBubble);
app3.use(Loading);
app3.mount("#main");

const notice = createApp({
    setup() {
        const onClose = () => {
            document.querySelector("#main").style = 'padding-top:0';
        }

        return { onClose };
    }
});
notice.use(NoticeBar);
notice.use(Swipe);
notice.use(SwipeItem);
notice.mount('#notice');

document.querySelector("#locate").addEventListener('touchend', function() {
    const scrollTop = document.querySelector("#locate").scrollTop; // 获取当前滚动距离
    if (scrollTop == 0) {
        isTop.value = true;
    } else if (isTop.value) {
        isTop.value = false;
    }
});

// const overlay = createApp({
//     setup() {
//         const show = ref(true);
//         return { show };
//     },
// });
// overlay.use(Overlay);
// overlay.mount("#overlay");

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => { 
    if (event.matches) {
        lightToDarkVar.value = false;
    } else {
        lightToDarkVar.value = true;
    }
    if (lightToDarkVar.value) {
        lightToDarkContent.value = "light";
        setTheme('light');
        localStorage.setItem("theme", true)
    } else {
        lightToDarkContent.value = "dark";
        setTheme('dark');
        localStorage.setItem("theme", false)
    }
});
