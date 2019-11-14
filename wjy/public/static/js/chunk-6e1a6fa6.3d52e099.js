(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-6e1a6fa6"],{2380:function(t,e,a){},"37cf":function(t,e,a){"use strict";var n=a("2380"),r=a.n(n);r.a},9406:function(t,e,a){"use strict";a.r(e);var n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"dashboard-editor-container"},[a("el-row",{staticClass:"panel-group",attrs:{gutter:40}},[a("el-col",{staticClass:"card-panel-col",attrs:{xs:12,sm:12,lg:6}},[a("div",{staticClass:"card-panel"},[a("div",{staticClass:"card-panel-icon-wrapper icon-people"},[a("svg-icon",{attrs:{"icon-class":"peoples","class-name":"card-panel-icon"}})],1),a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("\n              会员总数\n            ")]),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.todayData.user_count,duration:3200}})],1)])]),a("el-col",{staticClass:"card-panel-col",attrs:{xs:12,sm:12,lg:6}},[a("div",{staticClass:"card-panel"},[a("div",{staticClass:"card-panel-icon-wrapper icon-message"},[a("svg-icon",{attrs:{"icon-class":"user","class-name":"card-panel-icon"}})],1),a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("\n              本周新增\n            ")]),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.todayData.user_count_week,duration:3200}})],1)])]),a("el-col",{staticClass:"card-panel-col",attrs:{xs:12,sm:12,lg:6}},[a("div",{staticClass:"card-panel"},[a("div",{staticClass:"card-panel-icon-wrapper icon-money"},[a("svg-icon",{attrs:{"icon-class":"user","class-name":"card-panel-icon"}})],1),a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("\n              今日新增\n            ")]),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.todayData.user_count_day,duration:3200}})],1)])]),a("el-col",{staticClass:"card-panel-col",attrs:{xs:12,sm:12,lg:6}},[a("div",{staticClass:"card-panel"},[a("div",{staticClass:"card-panel-icon-wrapper icon-shopping"},[a("svg-icon",{attrs:{"icon-class":"withdraw","class-name":"card-panel-icon"}})],1),a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("\n              今日提现\n            ")]),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.todayData.withdraw_day,duration:3200}})],1)])]),a("el-col",{staticClass:"card-panel-col",attrs:{xs:12,sm:12,lg:6}},[a("div",{staticClass:"card-panel"},[a("div",{staticClass:"card-panel-icon-wrapper icon-people"},[a("svg-icon",{attrs:{"icon-class":"money","class-name":"card-panel-icon"}})],1),a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("\n                        已充值人数\n                    ")]),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.todayData.recharge_user,duration:3200}})],1)])]),a("el-col",{staticClass:"card-panel-col",attrs:{xs:12,sm:12,lg:6}},[a("div",{staticClass:"card-panel"},[a("div",{staticClass:"card-panel-icon-wrapper icon-message"},[a("svg-icon",{attrs:{"icon-class":"active","class-name":"card-panel-icon"}})],1),a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("\n                        今日日活\n                    ")]),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.todayData.active_day,duration:3200}})],1)])]),a("el-col",{staticClass:"card-panel-col",attrs:{xs:12,sm:12,lg:6}},[a("div",{staticClass:"card-panel"},[a("div",{staticClass:"card-panel-icon-wrapper icon-shopping"},[a("svg-icon",{attrs:{"icon-class":"redgroup","class-name":"card-panel-icon"}})],1),a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("\n                        红包群数量\n                    ")]),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.todayData.redgroup_count,duration:3200}})],1)])]),a("el-col",{staticClass:"card-panel-col",attrs:{xs:12,sm:12,lg:6}},[a("div",{staticClass:"card-panel"},[a("div",{staticClass:"card-panel-icon-wrapper icon-money"},[a("svg-icon",{attrs:{"icon-class":"tree","class-name":"card-panel-icon"}})],1),a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("\n                        财富树数量\n                    ")]),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.todayData.tree_count,duration:3200}})],1)])])],1),a("p",{staticClass:"title"},[t._v("统计详情\n          "),a("el-select",{staticClass:"filter-item",staticStyle:{width:"130px","margin-left":"10px"},attrs:{clearable:"",placeholder:"选择币种"},on:{change:t.handleFilter},model:{value:t.currencyNow_id,callback:function(e){t.currencyNow_id=e},expression:"currencyNow_id"}},t._l(t.list,function(t,e){return a("el-option",{key:e,attrs:{label:t.name,value:t.id}})}),1)],1),a("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],staticStyle:{width:"100%"},attrs:{data:t.currencyList,"empty-text":"暂无数据",border:"",fit:"","highlight-current-row":""}},[a("el-table-column",{attrs:{label:"币种",prop:"name",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[a("img",{staticStyle:{"vertical-align":"middle"},attrs:{width:"50",src:e.row.icon,alt:"scope.row.name"}}),t._v(" "+t._s(e.row.name))])]}}])}),a("el-table-column",{attrs:{label:"平台存量",prop:"stock",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.stock))])]}}])}),a("el-table-column",{attrs:{label:"今日充值",prop:"recharge_today",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.recharge_day))])]}}])}),a("el-table-column",{attrs:{label:"今日提现",prop:"cash_today",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.withdraw_day))])]}}])}),a("el-table-column",{attrs:{label:"红包群数量",prop:"redgroup_count",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.redgroup_count))])]}}])}),a("el-table-column",{attrs:{label:"财富树数量",prop:"tree_count",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.tree_count))])]}}])}),a("el-table-column",{attrs:{align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.redProfit)+"/"+t._s(e.row.redProfit_today))])]}}])},[a("template",{slot:"header"},[a("span",{directives:[{name:"popover",rawName:"v-popover:popover1",arg:"popover1"}]},[t._v("红包总收益/今日收益")]),a("el-popover",{ref:"popover1",attrs:{placement:"top-start",width:"200",trigger:"hover",content:"1、群主红包押金 2、全平台每个红包5%的抽成"}})],1)],2),a("el-table-column",{attrs:{align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.treeProfit)+"/"+t._s(e.row.treeProfit_today))])]}}])},[a("template",{slot:"header"},[a("span",{directives:[{name:"popover",rawName:"v-popover:popover2",arg:"popover2"}]},[t._v("财富树总收益/今日收益")]),a("el-popover",{ref:"popover2",attrs:{placement:"top-start",width:"200",trigger:"hover",content:"1、创建者缴纳的创建费用 2、平台做为金字塔顶端收取的下级升级费用3、等级不够，下级资金就交给了平台"}})],1)],2),a("el-table-column",{attrs:{align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.disProfit)+"/"+t._s(e.row.disProfit_today))])]}}])},[a("template",{slot:"header"},[a("span",{directives:[{name:"popover",rawName:"v-popover:popover3",arg:"popover3"}]},[t._v("推广总收益/今日收益")]),a("el-popover",{ref:"popover3",attrs:{placement:"top-start",width:"200",trigger:"hover",content:"平台推荐码注册用户的群主收益、红包流水、财富树收益的1级6%2级5%3级4%"}})],1)],2),a("el-table-column",{attrs:{align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.serviceProfit)+"/"+t._s(e.row.serviceProfit_today))])]}}])},[a("template",{slot:"header"},[a("span",{directives:[{name:"popover",rawName:"v-popover:popover4",arg:"popover4"}]},[t._v("提现总收益/今日收益")]),a("el-popover",{ref:"popover4",attrs:{placement:"top-start",width:"200",trigger:"hover",content:"提现手续费：0.5%"}})],1)],2),a("el-table-column",{attrs:{prop:"",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.totalProfit)+"/"+t._s(e.row.totalProfit_today))])]}}])},[a("template",{slot:"header"},[a("span",{directives:[{name:"popover",rawName:"v-popover:popover5",arg:"popover5"}]},[t._v("平台总收益/今日收益")]),a("el-popover",{ref:"popover5",attrs:{placement:"top-start",width:"200",trigger:"hover",content:"财富树收益、红包收益、推广收益、提现手续费收益的总和"}})],1)],2),a("el-table-column",{attrs:{label:"平台余额",prop:"",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.platform_money))]),a("br"),a("span",{staticStyle:{color:"#409EFF"},on:{click:function(a){return t.settlement(e.row)}}},[t._v("(点击结算)")])]}}])})],1),a("el-dialog",{attrs:{title:"结算/结算记录",visible:t.dialogSettlementVisible,width:"40%"},on:{"update:visible":function(e){t.dialogSettlementVisible=e}}},[a("div",{staticClass:"filter-container"},[a("el-input",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{placeholder:"结算金额"},model:{value:t.settlementCount,callback:function(e){t.settlementCount=e},expression:"settlementCount"}}),a("el-button",{staticClass:"filter-item",staticStyle:{"margin-left":"10px"},attrs:{type:"primary"},on:{click:t.settlementSubmit}},[t._v("\n                  结算\n              ")])],1),a("el-table",{staticStyle:{width:"100%","margin-top":"20px"},attrs:{data:t.settlementList,"empty-text":"暂无数据",border:"",fit:"","highlight-current-row":""}},[a("el-table-column",{attrs:{label:"币种名称",prop:"currency_name",align:"center"}},[[a("span",[t._v(t._s(t.settlementRow.name))])]],2),a("el-table-column",{attrs:{label:"数量",prop:"count",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.count))])]}}])}),a("el-table-column",{attrs:{label:"结算时间",prop:"lock",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.create_time))])]}}])})],1)],1)],1)},r=[],s=a("b775");function i(t){return Object(s["a"])({url:"/dashboard/index",method:"get",params:t})}function o(t){return Object(s["a"])({url:"/dashboard/profit",method:"get",params:t})}function l(t){return Object(s["a"])({url:"/dashboard/settlementList",method:"get",params:t})}function c(t){return Object(s["a"])({url:"/dashboard/settlement",method:"post",data:t})}var u=a("b9a9"),d=a("ec1b"),p=a.n(d),m={newVisitis:{expectedData:[100,120,161,134,105,160,165],actualData:[120,82,91,154,162,140,145]},messages:{expectedData:[200,192,120,144,160,130,140],actualData:[180,160,151,106,145,150,130]},purchases:{expectedData:[80,100,121,104,105,90,100],actualData:[120,90,100,138,142,130,130]},shoppings:{expectedData:[130,140,141,142,145,150,160],actualData:[120,82,91,154,162,140,130]}},f={name:"Dashboard",components:{CountTo:p.a},data:function(){return{lineChartData:m.newVisitis,todayData:{user_count:0,user_count_day:0,withdraw_day:0,recharge_day:0},profitData:{},currencyList:[],currencyNow_id:"",list:[],listLoading:!1,settlementRow:{},dialogSettlementVisible:!1,settlementList:[],settlementCount:0}},mounted:function(){this.getIndex(),this.getList()},methods:{getIndex:function(){var t=this;i().then(function(e){t.todayData=e.data})},getList:function(){var t=this;Object(u["c"])({}).then(function(e){t.list=e.data.list,t.getProfit()})},getProfit:function(){var t=this;this.listLoading=!0,o({currency_id:this.currencyNow_id}).then(function(e){t.profitData=e.data.profitData,t.currencyList=e.data.currency,t.listLoading=!1})},handleFilter:function(){this.getProfit()},handleSetLineChartData:function(t){this.lineChartData=m[t]},settlement:function(t){this.settlementRow=t,this.settlementCount=t.platform_money,this.dialogSettlementVisible=!0,this.getSettlement()},getSettlement:function(){var t=this;l({currency_id:this.settlementRow.id}).then(function(e){t.settlementList=e.data.list})},settlementSubmit:function(){var t=this;this.settlementCount<=0?this.$message.error("结算金额不合法"):this.settlementCount>this.settlementRow.platform_money?this.$message.error("余额不足"):c({currency_id:this.settlementRow.id,count:this.settlementCount}).then(function(e){1===e.data.state?(t.$message({message:"结算成功",type:"success"}),t.dialogSettlementVisible=!1,t.getProfit()):t.$message.error("结算失败:"+e.data.msg)})}}},h=f,v=(a("37cf"),a("2877")),_=Object(v["a"])(h,n,r,!1,null,"8f516a9a",null);e["default"]=_.exports},b9a9:function(t,e,a){"use strict";a.d(e,"c",function(){return r}),a.d(e,"a",function(){return s}),a.d(e,"b",function(){return i});var n=a("b775");function r(t){return Object(n["a"])({url:"/currency/list",method:"get",params:t})}function s(t){return Object(n["a"])({url:"/currency/create",method:"post",data:t})}function i(t){return Object(n["a"])({url:"/currency/delete",method:"post",data:t})}},ec1b:function(t,e,a){!function(e,a){t.exports=a()}(0,function(){return function(t){function e(n){if(a[n])return a[n].exports;var r=a[n]={i:n,l:!1,exports:{}};return t[n].call(r.exports,r,r.exports,e),r.l=!0,r.exports}var a={};return e.m=t,e.c=a,e.i=function(t){return t},e.d=function(t,a,n){e.o(t,a)||Object.defineProperty(t,a,{configurable:!1,enumerable:!0,get:n})},e.n=function(t){var a=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(a,"a",a),a},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p="/dist/",e(e.s=2)}([function(t,e,a){var n=a(4)(a(1),a(5),null,null);t.exports=n.exports},function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=a(3);e.default={props:{startVal:{type:Number,required:!1,default:0},endVal:{type:Number,required:!1,default:2017},duration:{type:Number,required:!1,default:3e3},autoplay:{type:Boolean,required:!1,default:!0},decimals:{type:Number,required:!1,default:0,validator:function(t){return t>=0}},decimal:{type:String,required:!1,default:"."},separator:{type:String,required:!1,default:","},prefix:{type:String,required:!1,default:""},suffix:{type:String,required:!1,default:""},useEasing:{type:Boolean,required:!1,default:!0},easingFn:{type:Function,default:function(t,e,a,n){return a*(1-Math.pow(2,-10*t/n))*1024/1023+e}}},data:function(){return{localStartVal:this.startVal,displayValue:this.formatNumber(this.startVal),printVal:null,paused:!1,localDuration:this.duration,startTime:null,timestamp:null,remaining:null,rAF:null}},computed:{countDown:function(){return this.startVal>this.endVal}},watch:{startVal:function(){this.autoplay&&this.start()},endVal:function(){this.autoplay&&this.start()}},mounted:function(){this.autoplay&&this.start(),this.$emit("mountedCallback")},methods:{start:function(){this.localStartVal=this.startVal,this.startTime=null,this.localDuration=this.duration,this.paused=!1,this.rAF=(0,n.requestAnimationFrame)(this.count)},pauseResume:function(){this.paused?(this.resume(),this.paused=!1):(this.pause(),this.paused=!0)},pause:function(){(0,n.cancelAnimationFrame)(this.rAF)},resume:function(){this.startTime=null,this.localDuration=+this.remaining,this.localStartVal=+this.printVal,(0,n.requestAnimationFrame)(this.count)},reset:function(){this.startTime=null,(0,n.cancelAnimationFrame)(this.rAF),this.displayValue=this.formatNumber(this.startVal)},count:function(t){this.startTime||(this.startTime=t),this.timestamp=t;var e=t-this.startTime;this.remaining=this.localDuration-e,this.useEasing?this.countDown?this.printVal=this.localStartVal-this.easingFn(e,0,this.localStartVal-this.endVal,this.localDuration):this.printVal=this.easingFn(e,this.localStartVal,this.endVal-this.localStartVal,this.localDuration):this.countDown?this.printVal=this.localStartVal-(this.localStartVal-this.endVal)*(e/this.localDuration):this.printVal=this.localStartVal+(this.localStartVal-this.startVal)*(e/this.localDuration),this.countDown?this.printVal=this.printVal<this.endVal?this.endVal:this.printVal:this.printVal=this.printVal>this.endVal?this.endVal:this.printVal,this.displayValue=this.formatNumber(this.printVal),e<this.localDuration?this.rAF=(0,n.requestAnimationFrame)(this.count):this.$emit("callback")},isNumber:function(t){return!isNaN(parseFloat(t))},formatNumber:function(t){t=t.toFixed(this.decimals),t+="";var e=t.split("."),a=e[0],n=e.length>1?this.decimal+e[1]:"",r=/(\d+)(\d{3})/;if(this.separator&&!this.isNumber(this.separator))for(;r.test(a);)a=a.replace(r,"$1"+this.separator+"$2");return this.prefix+a+n+this.suffix}},destroyed:function(){(0,n.cancelAnimationFrame)(this.rAF)}}},function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=a(0),r=function(t){return t&&t.__esModule?t:{default:t}}(n);e.default=r.default,"undefined"!=typeof window&&window.Vue&&window.Vue.component("count-to",r.default)},function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=0,r="webkit moz ms o".split(" "),s=void 0,i=void 0;if("undefined"==typeof window)e.requestAnimationFrame=s=function(){},e.cancelAnimationFrame=i=function(){};else{e.requestAnimationFrame=s=window.requestAnimationFrame,e.cancelAnimationFrame=i=window.cancelAnimationFrame;for(var o=void 0,l=0;l<r.length&&(!s||!i);l++)o=r[l],e.requestAnimationFrame=s=s||window[o+"RequestAnimationFrame"],e.cancelAnimationFrame=i=i||window[o+"CancelAnimationFrame"]||window[o+"CancelRequestAnimationFrame"];s&&i||(e.requestAnimationFrame=s=function(t){var e=(new Date).getTime(),a=Math.max(0,16-(e-n)),r=window.setTimeout(function(){t(e+a)},a);return n=e+a,r},e.cancelAnimationFrame=i=function(t){window.clearTimeout(t)})}e.requestAnimationFrame=s,e.cancelAnimationFrame=i},function(t,e){t.exports=function(t,e,a,n){var r,s=t=t||{},i=typeof t.default;"object"!==i&&"function"!==i||(r=t,s=t.default);var o="function"==typeof s?s.options:s;if(e&&(o.render=e.render,o.staticRenderFns=e.staticRenderFns),a&&(o._scopeId=a),n){var l=Object.create(o.computed||null);Object.keys(n).forEach(function(t){var e=n[t];l[t]=function(){return e}}),o.computed=l}return{esModule:r,exports:s,options:o}}},function(t,e){t.exports={render:function(){var t=this,e=t.$createElement;return(t._self._c||e)("span",[t._v("\n  "+t._s(t.displayValue)+"\n")])},staticRenderFns:[]}}])})}}]);