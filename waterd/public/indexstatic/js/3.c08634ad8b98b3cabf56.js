webpackJsonp([3],{E4LH:function(t,e,a){"use strict";e.b=function(t){return!!/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/.test(t)},e.a=function(t){return!!/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/.test(t)}},VvTn:function(t,e){var a={floatAdd:function(t,e){var a,r,i;try{a=t.toString().split(".")[1].length}catch(t){a=0}try{r=e.toString().split(".")[1].length}catch(t){r=0}return(t*(i=Math.pow(10,Math.max(a,r)))+e*i)/i},floatSub:function(t,e){var a,r,i;try{a=t.toString().split(".")[1].length}catch(t){a=0}try{r=e.toString().split(".")[1].length}catch(t){r=0}return((t*(i=Math.pow(10,Math.max(a,r)))-e*i)/i).toFixed(a>=r?a:r)},floatMul:function(t,e){var a=0,r=t.toString(),i=e.toString();try{a+=r.split(".")[1].length}catch(t){}try{a+=i.split(".")[1].length}catch(t){}return Number(r.replace(".",""))*Number(i.replace(".",""))/Math.pow(10,a)},floatDiv:function(t,e){var a=0,r=0;try{a=t.toString().split(".")[1].length}catch(t){}try{r=e.toString().split(".")[1].length}catch(t){}return Number(t.toString().replace(".",""))/Number(e.toString().replace(".",""))*Math.pow(10,r-a)},decimalLength:function(t){return String(t).indexOf(".")+1>0?t.toString().split(".")[1].length:0},random:function(t,e){return Math.floor(Math.random()*(e-t))+t}};t.exports=a},haEB:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var r=a("bGai"),i=a("E4LH"),s=a("ArgV"),n=a("VvTn"),o=a.n(n),l=a("dQyo"),c=a.n(l),h={name:"Center",components:{Navbar:r.b,Centerbar:r.a},data:function(){var t=this;return{withdrawdisable:!1,transferdisable:!1,walletData:{},withdrawDialogVisible:!1,transferDialogVisible:!1,withdrawForm:{address:"",money:""},transferForm:{username:"",money:""},withdrawInfo:{cash_service_ratio:0,cash_service_max:0,cash_min:0,cash_max:0},serviceMoney:0,realMoney:0,withdrawrules:{address:[{required:!0,message:"地址不能为空",trigger:"blur"}],money:[{required:!0,validator:function(e,a,r){if(!a)return r(new Error("提现金额不能为空"));Object(i.b)(a)?a<=0||a<t.withdrawInfo.cash_min&&t.withdrawInfo.cash_min>0?r(new Error("提现金额必须大于 "+t.withdrawInfo.cash_min)):a>t.withdrawInfo.cash_max&&t.withdrawInfo.cash_max>0?r(new Error("提现金额最大为 "+t.withdrawInfo.cash_max)):a>t.walletData.money?r(new Error("余额不足")):r():r(new Error("请输入合法金额"))},trigger:"blur"}]},transferrules:{username:[{required:!0,message:"账号信息不能为空",trigger:"blur"}],money:[{required:!0,validator:function(e,a,r){if(!a)return r(new Error("提现金额不能为空"));Object(i.b)(a)?a<1?r(new Error("提现金额必须大于 1")):a>t.walletData.money?r(new Error("余额不足")):r():r(new Error("请输入合法金额"))},trigger:"blur"}]}}},watch:{withdrawForm:{handler:function(t,e){var a=this.withdrawInfo.cash_service_ratio/100,r=o.a.floatMul(a,this.withdrawForm.money);this.withdrawInfo.cash_service_max>0&&r>this.withdrawInfo.cash_service_max?this.serviceMoney=this.withdrawInfo.cash_service_max:this.serviceMoney=r,this.realMoney=o.a.floatSub(this.withdrawForm.money,this.serviceMoney)},immediate:!1,deep:!0}},mounted:function(){this.getWallet(),this.getWithdrawInfo()},methods:{getWallet:function(){var t=this;Object(s.f)().then(function(e){t.walletData=e})},getWithdrawInfo:function(){var t=this;Object(s.h)().then(function(e){t.withdrawInfo=e})},copy:function(t){var e=this,a=new c.a(".copy",{text:function(){return t}});a.on("success",function(t){e.$message({message:"复制成功",type:"success"}),a.destroy()}),a.on("error",function(t){e.$message({message:"复制失败, 当前浏览器不支持",type:"error"}),a.destroy()})},flashbuy:function(){var t="https://buy.flashbuy.io/#/trade?name=usdt-erc20&cny=0&address="+this.walletData.address;window.open(t)},submitWithdraw:function(){var t=this;this.$refs.withdrawForm.validate(function(e){e&&(t.withdrawdisable=!0,Object(s.g)(t.withdrawForm).then(function(e){t.withdrawdisable=!1,t.withdrawDialogVisible=!1,t.walletData.money=e.balance,t.$notify({title:"提现通知",message:"提现成功",type:"success"})}))})},submitTransfer:function(){var t=this;this.$refs.transferForm.validate(function(e){e&&(t.transferdisable=!0,Object(s.c)(t.transferForm).then(function(e){t.transferdisable=!1,t.transferDialogVisible=!1,t.walletData.money=e.balance,t.$notify({title:"转账通知",message:"转账成功",type:"success"})}))})}}},f={render:function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"box"},[r("navbar"),t._v(" "),r("div",{staticClass:"content"},[r("div",{staticClass:"center-box warp-1200 ov"},[r("centerbar"),t._v(" "),r("div",{staticClass:"right fl"},[t._m(0),t._v(" "),r("div",{staticClass:"ba-box ov"},[r("p",{staticClass:"fl balance-b"},[r("span",[t._v("余额")]),t._v(" "),r("i",[t._v(t._s(t.walletData.money)+" ")])]),t._v(" "),r("div",{staticClass:"fr tx-btn ov"},[r("p",{staticClass:"fl"},[r("img",{attrs:{src:a("wq9f"),width:"25",height:"22",alt:""}}),r("span",{on:{click:function(e){t.withdrawDialogVisible=!0}}},[t._v("提现")])]),t._v(" "),r("p",{staticClass:"fl"},[r("img",{attrs:{src:a("wq9f"),width:"25",height:"22",alt:""}}),r("span",{on:{click:function(e){t.transferDialogVisible=!0}}},[t._v("内部转账")])]),t._v(" "),r("p",{staticClass:"fl"},[r("img",{attrs:{src:a("wq9f"),width:"25",height:"22",alt:""}}),r("span",{on:{click:t.flashbuy}},[t._v("人民币充值提现USDT")])])])]),t._v(" "),t._m(1),t._v(" "),r("div",{staticClass:"ba-box ov"},[r("p",{staticClass:"fl balance-b",staticStyle:{"margin-top":"24px"}},[r("span",[t._v(t._s(t.walletData.address))]),r("br"),r("br"),r("span",{staticStyle:{color:"red"}},[t._v("注意:本地址只接受erc20版本的usdt，请勿充值其它币种，否则无法找回")])]),t._v(" "),r("div",{staticClass:"fr tx-btn ov"},[r("img",{staticClass:"fl",staticStyle:{width:"70px",height:"70px",background:"#333",display:"inline-block"},attrs:{src:t.walletData.address_qrcode,alt:""}}),t._v(" "),r("p",{staticClass:"fl copy",on:{click:function(e){return t.copy(t.walletData.address)}}},[r("img",{attrs:{src:a("wq9f"),width:"25",height:"22",alt:""}}),r("span",[t._v("复制地址")])])])])])],1)]),t._v(" "),r("el-dialog",{attrs:{title:"提现",visible:t.withdrawDialogVisible,width:"30%",center:""},on:{"update:visible":function(e){t.withdrawDialogVisible=e}}},[r("el-form",{ref:"withdrawForm",attrs:{rules:t.withdrawrules,model:t.withdrawForm,"label-position":"left","label-width":"120px"}},[r("el-form-item",{attrs:{label:"地址",prop:"address"}},[r("el-input",{model:{value:t.withdrawForm.address,callback:function(e){t.$set(t.withdrawForm,"address",e)},expression:"withdrawForm.address"}})],1),t._v(" "),r("el-form-item",{attrs:{label:"金额",prop:"money"}},[r("el-input",{model:{value:t.withdrawForm.money,callback:function(e){t.$set(t.withdrawForm,"money",e)},expression:"withdrawForm.money"}})],1),t._v(" "),r("el-form-item",{attrs:{label:""}},[r("div",{staticClass:"el-block-help"},[r("span",{staticStyle:{float:"left"}},[t._v("手续费:"+t._s(t.serviceMoney))]),r("span",{staticStyle:{float:"right"}},[t._v("实际到账:"+t._s(t.realMoney))])])])],1),t._v(" "),r("span",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[r("el-button",{on:{click:function(e){t.withdrawDialogVisible=!1}}},[t._v("取 消")]),t._v(" "),r("el-button",{attrs:{type:"primary",disabled:t.withdrawdisable},on:{click:t.submitWithdraw}},[t._v("确 定")])],1)],1),t._v(" "),r("el-dialog",{attrs:{title:"转账",visible:t.transferDialogVisible,width:"30%",center:""},on:{"update:visible":function(e){t.transferDialogVisible=e}}},[r("el-form",{ref:"transferForm",attrs:{rules:t.transferrules,model:t.transferForm,"label-position":"left","label-width":"120px"}},[r("el-form-item",{attrs:{label:"对方账号",prop:"username"}},[r("el-input",{model:{value:t.transferForm.username,callback:function(e){t.$set(t.transferForm,"username",e)},expression:"transferForm.username"}})],1),t._v(" "),r("el-form-item",{attrs:{label:"金额",prop:"money"}},[r("el-input",{model:{value:t.transferForm.money,callback:function(e){t.$set(t.transferForm,"money",e)},expression:"transferForm.money"}})],1),t._v(" "),r("el-form-item",{attrs:{label:""}},[r("div",{staticClass:"el-block-help"},[t._v("转账的账号必须是用户登录的账号")])])],1),t._v(" "),r("span",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[r("el-button",{on:{click:function(e){t.transferDialogVisible=!1}}},[t._v("取 消")]),t._v(" "),r("el-button",{attrs:{type:"primary",disabled:t.transferdisable},on:{click:t.submitTransfer}},[t._v("确 定")])],1)],1)],1)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("p",{staticClass:"balance"},[e("span",[this._v("余额")])])},function(){var t=this.$createElement,e=this._self._c||t;return e("p",{staticClass:"balance"},[e("span",[this._v("地址")])])}]};var d=a("C7Lr")(h,f,!1,function(t){a("thbZ")},"data-v-5ebfd14f",null);e.default=d.exports},thbZ:function(t,e){},wq9f:function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAAWCAYAAAA1vze2AAADIklEQVRIS62WW2hcVRSGv//MTMakiZoiXoaABh8UfPDSiHkQhCptGmZqqqYP9cFqO5N6SUGpFKTUILbok9CitZMESm2tLcTLzFgJlor4oERE0RdFhCqJeA1Re5k0M+eXMzVSKyV46j5ve7PWt/6z/r3OUf8ht7a3cC8mB7QI6lzgCiEQ1BxyVAH7la94q8ygzaeIUzK6QEYj3CDBjcARFSr+3ubdVMDm47PMtf0fBGAuhRBPYR5RoeRfgDEyPBZ+yxVK84RCFsVhOUorqj7Nro7P+WpqCc/KbFK+7J8lxphi0BleFdwXB3B2jOHIcI5l+TLPyGxWoexpmwMzVTZekuaGRMBuu/EuXyHgJCZYCOpGC0gA/ZgfA7Fhd04fFsp+GrM1UvK6oMvmTQKmMPcIZoo5LV8o+bnnhYpfsFkqczAUiwPTh/hD68Z8dZBkm8R1iIShUzARBzJQ8YuheUDia0JqFj/YbGvY9f59vviiNi4PRBqxXbComNNdjUqHHBRuZgUpviz26pv56h96y5lEkls0yXhxQHPRfr7sXRJdIWxwnd9OnmDmwBp+/cedWLHD6Y5reE2ibR6ypuL2VhP17SMn6R/p1eTaN3xpU5K9QO50jc49q3TsbwgsqafoGe3R9HxBC0IawSVnBTsRn9VDnkwmeDw0fYYtI1n2IEWNP6MkLiRKsL7kpQFsb7hINANbMllKQ1I4X3FMiLW+xG2COyLHOeBWmXU2Y5hxxGLDxyMreQ/kWJD+Q25qb+F54E6b6GmT6MQcM/yuKDNMTCZ59J1ezcaCRM7K30Q3CbplaojrgYcNw4gvVKcpTDAxktUHsXsyeNjpashemZ5Ih0UysrfhxF/QyDjvFz+hjyGF8ZQAhbJbMJc1mituB/bbPCg4Gm0lq0y/tFrHYyv519goexkwbnH3cFalc89jKzk70dq3fWUqZKPMy8WV+i4e5LDTHTUOSjTHmV35snfKdNebWH7eG9+wbDP7gGsxq+aqnEonF/4cz9ZwopW06uyQydSr9I6uPs9YAStfZkDiOcxP/LefimiCZwSjXMWmYteZoRmtPwE/15uB3tds6AAAAABJRU5ErkJggg=="}});
//# sourceMappingURL=3.c08634ad8b98b3cabf56.js.map