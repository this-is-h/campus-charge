<script>
    import { ref } from 'vue';
    import { showToast } from 'vant';

    export default {
        setup() {
            const showPopover = ref(false);

            // 通过 actions 属性来定义菜单选项
            const actions = [
                { text: '关于我们' },
                { text: '常见问题' },
            ];
            const onSelect = (action) => {
                switch(action) {
                    case "关于我们":
                        location.href = 'about.html'
                }

            };

            return {
            actions,
            onSelect,
            showPopover,
            };
        },
    };
</script>

<template>
    <van-popover v-model:show="showPopover" :actions="actions" @select="onSelect" placement="bottom-end">
        <template #reference>
            <div class="container" id="container" onclick="changeMenu()">
                <div class="first_block" id="first" style=""></div>
                <div class="center_block" id="second" style=""></div>
                <div class="third_block" id="third" style=""></div>
            </div>
        </template>
    </van-popover>
</template>

<style>
    .container {
        position: relative;
        width: 20px;
        height: 20px;
        transform: translateX(-58%);
        display: flex;
        justify-content: space-around;
        align-items: center;
        flex-direction: column;
        cursor: pointer;
        /* filter: drop-shadow(0px 10px 5px rgba(0, 0, 0, 0.2)); */
    }
    .first_block,
    .center_block,
    .third_block {
        position: absolute;
        width: 100%;
        height: 17%;
        border-radius: 16px;
        background-color: #fff;
    }
    /* before */
    .container .first_block {
        top: 7%;
        animation: firstLine-rev ease-in-out 0.4s forwards;
    }
    .container .third_block {
        bottom: 7%;
        animation: thirdLine-rev ease-in-out 0.4s forwards;
    }
    .container .center_block {
        animation: centerLine-rev ease-in-out 0.4s forwards;
    }
    .active .first_block {
        animation: firstLine ease-in-out 0.4s forwards;
    }
    .active .third_block {
        animation: thirdLine ease-in-out 0.4s forwards;
    }
    .active .center_block {
        animation: centerLine ease-in-out 0.4s forwards;
    }

    @keyframes firstLine {
        0% {
        }
        50% {
            top: 50%;
            transform: translateY(-50%);
        }
        100% {
            top: 50%;
            transform: translateY(-50%) rotate(45deg);
        }
    }
    @keyframes firstLine-rev {
        0% {
            top: 50%;
            transform: translateY(-50%) rotate(45deg);
        }
        50% {
            top: 50%;
            transform: translateY(-50%) rotate(0deg);
        }
    }

    @keyframes thirdLine {
        0% {
        }
        50% {
            bottom: 50%;
            transform: translateY(50%);
        }
        100% {
            bottom: 50%;
            transform: translateY(50%) rotate(135deg);
        }
    }
    @keyframes thirdLine-rev {
        0% {
            bottom: 50%;
            transform: translateY(50%) rotate(135deg);
        }
        50% {
            bottom: 50%;
            transform: translateY(50%) rotate(0deg);
        }
    }

    @keyframes centerLine {
        0% {
            transform: scale(1);
        }
        100% {
            transform: scale(0);
        }
    }
    @keyframes centerLine-rev {
        0% {
            transform: scale(0);
        }
        50% {
            transform: scale(0);
        }
        100% {
            transform: scale(1);
        }
    }
</style>