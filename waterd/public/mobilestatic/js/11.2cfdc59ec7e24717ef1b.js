webpackJsonp([11],{K3vK:function(t,a){},qbn8:function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});e("hxht");var s=e("BdGv"),i=(e("qD+w"),e("T0tl")),n=e("ArgV"),r=e("VvTn"),o=e.n(r),c=e("J56h"),l=(e("0F10"),e("E4LH")),d={name:"Withdraw",components:{navbar:c.a},data:function(){return{walletData:{},withdrawForm:{address:"",money:""},withdrawInfo:{cash_service_ratio:0,cash_service_max:0,cash_min:0,cash_max:0},serviceMoney:0,realMoney:0}},watch:{withdrawForm:{handler:function(t,a){var e=this.withdrawInfo.cash_service_ratio/100,s=o.a.floatMul(e,this.withdrawForm.money);this.withdrawInfo.cash_service_max>0&&s>this.withdrawInfo.cash_service_max?this.serviceMoney=this.withdrawInfo.cash_service_max:this.serviceMoney=s,this.realMoney=o.a.floatSub(this.withdrawForm.money,this.serviceMoney)},immediate:!1,deep:!0}},mounted:function(){this.getWallet(),this.getWithdrawInfo()},methods:{getWallet:function(){var t=this;Object(n.j)().then(function(a){t.walletData=a})},getWithdrawInfo:function(){var t=this;Object(n.l)().then(function(a){t.withdrawInfo=a})},withdrawAll:function(){this.withdrawForm.money=this.walletData.money},withdrawSub:function(){var t=this,a=this.withdrawForm;return""==a.address?(Object(i.a)("请输入提现地址"),!1):""==a.money?(Object(i.a)("请输入转账金额"),!1):Object(l.b)(a.money)?Number(a.money)>Number(this.walletData.money)?(Object(i.a)("余额不足"),!1):void Object(n.k)(a).then(function(a){t.walletData.money=a.balance,s.a.alert({title:"系统提醒",message:"提现成功"}).then(function(){})}):(Object(i.a)("请输入合法金额"),!1)}}},v={render:function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",["Android"==t.$phonemodel?e("navbar",{attrs:{title:"提现"}}):t._e(),t._v(" "),e("div",{staticClass:"app",class:{nav:"Android"==t.$phonemodel}},[e("p",{staticClass:"title"},[t._v("USDT")]),t._v(" "),e("div",{staticClass:"addr"},[e("p",[t._v("提币地址")]),t._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:t.withdrawForm.address,expression:"withdrawForm.address"}],attrs:{type:"text",placeholder:"请输入"},domProps:{value:t.withdrawForm.address},on:{input:function(a){a.target.composing||t.$set(t.withdrawForm,"address",a.target.value)}}})]),t._v(" "),e("div",{staticClass:"addr pr"},[e("p",[t._v("提币数量")]),t._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:t.withdrawForm.money,expression:"withdrawForm.money"}],attrs:{type:"text",placeholder:"请输入"},domProps:{value:t.withdrawForm.money},on:{input:function(a){a.target.composing||t.$set(t.withdrawForm,"money",a.target.value)}}}),t._v(" "),e("span",[t._v("可用："+t._s(t.walletData.money)+"USDT")])]),t._v(" "),e("p",{staticClass:"ov all"},[e("span",{staticClass:"fl"}),e("span",{staticClass:"fr",on:{click:t.withdrawAll}},[t._v("全部提取")])]),t._v(" "),e("p",{staticClass:"ov service"},[e("span",{staticClass:"fl"},[t._v("手续费:")]),e("span",{staticClass:"fr"},[t._v(t._s(t.serviceMoney)+"USDT ")])]),t._v(" "),e("p",{staticClass:"ov service"},[e("span",{staticClass:"fl"},[t._v("实际到账:")]),e("span",{staticClass:"fr"},[t._v(t._s(t.realMoney)+"USDT ")])]),t._v(" "),e("p",{staticClass:"ov service"},[e("span",{staticClass:"fl"},[t._v("最低提币金额:")]),e("span",{staticClass:"fr"},[t._v(t._s(t.withdrawInfo.cash_min)+"USDT ")])]),t._v(" "),e("div",{staticClass:"btn"},[e("button",{on:{click:t.withdrawSub}},[t._v("确定")])]),t._v(" "),e("div",{staticClass:"art"})])],1)},staticRenderFns:[]};var h=e("VU/8")(d,v,!1,function(t){e("K3vK")},"data-v-de1f1e08",null);a.default=h.exports}});
//# sourceMappingURL=11.2cfdc59ec7e24717ef1b.js.map