(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-44bf9d70"],{"16b2":function(e,t,n){"use strict";var a=n("72dc"),r=n.n(a);r.a},"17f2":function(e,t,n){},"333d":function(e,t,n){"use strict";var a=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"pagination-container",class:{hidden:e.hidden}},[n("el-pagination",{attrs:{background:e.background,"current-page":e.currentPage,"page-size":e.pageSize,layout:e.layout,"page-sizes":e.pageSizes,total:e.total},on:{"update:currentPage":function(t){e.currentPage=t},"update:current-page":function(t){e.currentPage=t},"update:pageSize":function(t){e.pageSize=t},"update:page-size":function(t){e.pageSize=t},"size-change":e.handleSizeChange,"current-change":e.handleCurrentChange}})],1)},r=[];n("c5f6");Math.easeInOutQuad=function(e,t,n,a){return e/=a/2,e<1?n/2*e*e+t:(e--,-n/2*(e*(e-2)-1)+t)};var i=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(e){window.setTimeout(e,1e3/60)}}();function l(e){document.documentElement.scrollTop=e,document.body.parentNode.scrollTop=e,document.body.scrollTop=e}function o(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function s(e,t,n){var a=o(),r=e-a,s=20,u=0;t="undefined"===typeof t?500:t;var c=function e(){u+=s;var o=Math.easeInOutQuad(u,a,r,t);l(o),u<t?i(e):n&&"function"===typeof n&&n()};c()}var u={name:"Pagination",props:{total:{required:!0,type:Number},page:{type:Number,default:1},limit:{type:Number,default:20},pageSizes:{type:Array,default:function(){return[10,20,30,50]}},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},background:{type:Boolean,default:!0},autoScroll:{type:Boolean,default:!0},hidden:{type:Boolean,default:!1}},computed:{currentPage:{get:function(){return this.page},set:function(e){this.$emit("update:page",e)}},pageSize:{get:function(){return this.limit},set:function(e){this.$emit("update:limit",e)}}},methods:{handleSizeChange:function(e){this.$emit("pagination",{page:this.currentPage,limit:e}),this.autoScroll&&s(0,800)},handleCurrentChange:function(e){this.$emit("pagination",{page:e,limit:this.pageSize}),this.autoScroll&&s(0,800)}}},c=u,d=(n("743e"),n("2877")),p=Object(d["a"])(c,a,r,!1,null,"1800b8ae",null);t["a"]=p.exports},"72dc":function(e,t,n){},"743e":function(e,t,n){"use strict";var a=n("17f2"),r=n.n(a);r.a},aa77:function(e,t,n){var a=n("5ca1"),r=n("be13"),i=n("79e5"),l=n("fdef"),o="["+l+"]",s="​",u=RegExp("^"+o+o+"*"),c=RegExp(o+o+"*$"),d=function(e,t,n){var r={},o=i(function(){return!!l[e]()||s[e]()!=s}),u=r[e]=o?t(p):l[e];n&&(r[n]=u),a(a.P+a.F*o,"String",r)},p=d.trim=function(e,t){return e=String(r(e)),1&t&&(e=e.replace(u,"")),2&t&&(e=e.replace(c,"")),e};e.exports=d},c5f6:function(e,t,n){"use strict";var a=n("7726"),r=n("69a8"),i=n("2d95"),l=n("5dbc"),o=n("6a99"),s=n("79e5"),u=n("9093").f,c=n("11e9").f,d=n("86cc").f,p=n("aa77").trim,f="Number",g=a[f],m=g,h=g.prototype,y=i(n("2aeb")(h))==f,b="trim"in String.prototype,v=function(e){var t=o(e,!1);if("string"==typeof t&&t.length>2){t=b?t.trim():p(t,3);var n,a,r,i=t.charCodeAt(0);if(43===i||45===i){if(n=t.charCodeAt(2),88===n||120===n)return NaN}else if(48===i){switch(t.charCodeAt(1)){case 66:case 98:a=2,r=49;break;case 79:case 111:a=8,r=55;break;default:return+t}for(var l,s=t.slice(2),u=0,c=s.length;u<c;u++)if(l=s.charCodeAt(u),l<48||l>r)return NaN;return parseInt(s,a)}}return+t};if(!g(" 0o1")||!g("0b1")||g("+0x1")){g=function(e){var t=arguments.length<1?0:e,n=this;return n instanceof g&&(y?s(function(){h.valueOf.call(n)}):i(n)!=f)?l(new m(v(t)),n,g):v(t)};for(var _,w=n("9e1e")?u(m):"MAX_VALUE,MIN_VALUE,NaN,NEGATIVE_INFINITY,POSITIVE_INFINITY,EPSILON,isFinite,isInteger,isNaN,isSafeInteger,MAX_SAFE_INTEGER,MIN_SAFE_INTEGER,parseFloat,parseInt,isInteger".split(","),k=0;w.length>k;k++)r(m,_=w[k])&&!r(g,_)&&d(g,_,c(m,_));g.prototype=h,h.constructor=g,n("2aba")(a,f,g)}},e382:function(e,t,n){"use strict";n.r(t);var a=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"app-container"},[n("div",{staticClass:"filter-container"},[n("el-input",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{placeholder:"账号 | 用户名"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.handleFilter(t)}},model:{value:e.listQuery.name,callback:function(t){e.$set(e.listQuery,"name",t)},expression:"listQuery.name"}}),n("el-input",{staticClass:"filter-item",staticStyle:{width:"200px","margin-left":"10px"},attrs:{placeholder:"推荐人"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.handleFilter(t)}},model:{value:e.listQuery.puser,callback:function(t){e.$set(e.listQuery,"puser",t)},expression:"listQuery.puser"}}),n("el-button",{staticClass:"filter-item",staticStyle:{"margin-left":"10px"},attrs:{type:"primary",icon:"el-icon-search"},on:{click:e.handleFilter}},[e._v("\n      搜索\n    ")])],1),n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.listLoading,expression:"listLoading"}],staticStyle:{width:"100%"},attrs:{data:e.list,"empty-text":"暂无数据",border:"",fit:"","highlight-current-row":""}},[n("el-table-column",{attrs:{label:"ID",prop:"uid",sortable:"custom",align:"center",width:"80"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("span",[e._v(e._s(t.row.uid))])]}}])}),n("el-table-column",{attrs:{label:"用户名",prop:"nickname",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("span",[e._v(e._s(t.row.nickname))])]}}])}),n("el-table-column",{attrs:{label:"账号",prop:"username",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("span",[e._v(e._s(t.row.username))])]}}])}),n("el-table-column",{attrs:{label:"推荐人",prop:"puser",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("span",[e._v(e._s(t.row.puser))])]}}])}),n("el-table-column",{attrs:{label:"邀请码",prop:"invite_code",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("span",[e._v(e._s(t.row.invite_code))])]}}])}),n("el-table-column",{attrs:{label:"注册时间",prop:"create_time",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("span",[e._v(e._s(t.row.create_time))])]}}])}),n("el-table-column",{attrs:{label:"钱包",align:"center",width:"230","class-name":"small-padding fixed-width"},scopedSlots:e._u([{key:"default",fn:function(t){var a=t.row;return[n("el-button",{attrs:{type:"primary",size:"mini"},on:{click:function(t){return e.openWallet(a)}}},[e._v("\n          钱包\n        ")])]}}])})],1),n("el-dialog",{attrs:{title:"个人钱包",visible:e.dialogWalletVisible,width:"40%"},on:{"update:visible":function(t){e.dialogWalletVisible=t}}},[n("el-table",{staticStyle:{width:"100%"},attrs:{data:e.walletData,"empty-text":"暂无数据",border:"",fit:"","highlight-current-row":""}},[n("el-table-column",{attrs:{label:"币种名称",prop:"currency_name",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("span",[e._v(e._s(t.row.currency_name))])]}}])}),n("el-table-column",{attrs:{label:"总金额",prop:"total",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("span",[n("el-tag",[e._v(e._s(t.row.total))])],1)]}}])}),n("el-table-column",{attrs:{label:"可用金额",prop:"free",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("span",[n("el-tag",{attrs:{type:"success"}},[e._v(e._s(t.row.free))])],1)]}}])}),n("el-table-column",{attrs:{label:"锁定金额",prop:"lock",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("span",[n("el-tag",{attrs:{type:"danger"}},[e._v(e._s(t.row.lock))])],1)]}}])}),n("el-table-column",{attrs:{label:"操作",align:"center",width:"230","class-name":"small-padding fixed-width"},scopedSlots:e._u([{key:"default",fn:function(t){var a=t.row;return[n("el-button",{attrs:{type:"primary",size:"mini"},on:{click:function(t){return e.gorecharge(a)}}},[e._v("\n            充值\n          ")])]}}])})],1)],1),n("el-dialog",{attrs:{title:"充值",visible:e.dialogRechargeVisible,width:"40%"},on:{"update:visible":function(t){e.dialogRechargeVisible=t}}},[n("el-form",{ref:"rechargeForm",staticStyle:{width:"80%","margin-left":"50px"},attrs:{rules:e.rechargerules,model:e.recharge,"label-position":"left","label-width":"100px"}},[n("el-form-item",{attrs:{label:"充值金额",prop:"money"}},[n("el-input",{model:{value:e.recharge.money,callback:function(t){e.$set(e.recharge,"money",t)},expression:"recharge.money"}})],1)],1),n("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[n("el-button",{on:{click:function(t){e.dialogRechargeVisible=!1}}},[e._v("\n                取消\n            ")]),n("el-button",{attrs:{type:"primary"},on:{click:e.rechargeCurrency}},[e._v("\n                确认\n            ")])],1)],1),n("pagination",{directives:[{name:"show",rawName:"v-show",value:e.total>0,expression:"total>0"}],attrs:{total:e.total,page:e.listQuery.page,limit:e.listQuery.limit},on:{"update:page":function(t){return e.$set(e.listQuery,"page",t)},"update:limit":function(t){return e.$set(e.listQuery,"limit",t)},pagination:e.getList}})],1)},r=[],i=n("b775");function l(e){return Object(i["a"])({url:"/user/list",method:"get",params:e})}function o(e){return Object(i["a"])({url:"/user/create",method:"post",data:e})}function s(e){return Object(i["a"])({url:"/user/delete",method:"post",data:e})}function u(e){return Object(i["a"])({url:"/user/recharge",method:"post",data:e})}function c(e){return Object(i["a"])({url:"/user/wallet",method:"get",params:e})}var d=n("333d"),p={name:"User",components:{Pagination:d["a"]},data:function(){return{list:null,total:0,listQuery:{page:1,limit:20,name:"",puser:""},wallet:[],dialogStatus:"",dialogFormVisible:!1,dialogWalletVisible:!1,walletData:[],recharge:{uid:"",currency_id:"",money:0},dialogRechargeVisible:!1,form:{id:"",username:"",realname:"",password:""},textMap:{create:"新增用户",update:"修改用户"},listLoading:!1,rules:{realname:[{required:!0,message:"姓名必须填写",trigger:"change"}],username:[{required:!0,message:"账号必须填写",trigger:"change"}]},rechargerules:{money:[{required:!0,message:"请填写充值金额",trigger:"change"}]}}},mounted:function(){this.getList()},methods:{handleFilter:function(){this.listQuery.page=1,this.getList()},getList:function(){var e=this;this.listLoading=!0,l(this.listQuery).then(function(t){e.list=t.data.list,e.total=t.data.total,setTimeout(function(){e.listLoading=!1},1e3)})},handleUpdate:function(e){this.form.id=e.id,this.form.username=e.username,this.form.realname=e.realname,this.form.password="",this.dialogFormVisible=!0,this.dialogStatus="update"},createData:function(){var e=this;this.$refs["dataForm"].validate(function(t){t&&o(e.form).then(function(t){e.dialogFormVisible=!1,2e4===t.code&&(e.$message({type:"success",message:"操作成功!"}),e.getList())})})},deleteConfirm:function(e){var t=this;this.$confirm("此操作将永久删除该记录, 是否继续?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){t.deleteData(e)}).catch(function(){t.$message({type:"info",message:"已取消删除"})})},deleteData:function(e){var t=this;s({id:e.id}).then(function(e){t.$message({type:"success",message:"删除成功!"}),t.getList()})},openWallet:function(e){var t=this;c({uid:e.uid}).then(function(e){t.dialogWalletVisible=!0,t.walletData=e.data})},returnImg:function(e){this.form.form_pic=e},gorecharge:function(e){this.dialogRechargeVisible=!0,this.recharge.uid=e.uid,this.recharge.currency_id=e.currency_id},rechargeCurrency:function(){var e=this;this.$refs["rechargeForm"].validate(function(t){t&&u(e.recharge).then(function(t){e.dialogRechargeVisible=!1,2e4===t.code&&(e.$message({type:"success",message:"操作成功!"}),c({uid:e.recharge.uid}).then(function(t){e.walletData=t.data}))})})}}},f=p,g=(n("16b2"),n("2877")),m=Object(g["a"])(f,a,r,!1,null,"08e161aa",null);t["default"]=m.exports},fdef:function(e,t){e.exports="\t\n\v\f\r   ᠎             　\u2028\u2029\ufeff"}}]);