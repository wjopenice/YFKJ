webpackJsonp([18],{"1g36":function(t,e,a){t.exports=a.p+"indexstatic/img/shangchuan_image.eb53292.png"},"8gPX":function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=a("Dd8w"),c=a.n(n),i=a("vMJZ"),s=(a("4vvA"),a("GKy3"),a("NYxO")),o={name:"Wechatcode",computed:c()({},Object(s.b)(["wechat"])),data:function(){return{setNew:!1}},mounted:function(){this.setNew=!this.wechat},methods:{headImgChange:function(t){var e=this,a=t.target.files[0],n=new FormData;n.append("file",a,a.name),Object(i.A)(n).then(function(t){e.setNew=!1,e.$store.dispatch("user/setWechat",t.imgUrl).then(function(){}).catch(function(){})})},clearCode:function(){this.setNew=!0}}},r={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"appbox"},[t.setNew?n("div",{staticClass:"contain"},[n("div",{staticClass:"title"},[t._v("上传微信二维码")]),t._v(" "),n("div",{staticClass:"subContain pr"},[n("img",{attrs:{src:a("1g36"),alt:""}}),t._v(" "),n("input",{attrs:{type:"file",accept:"image/*",id:"upBtn"},on:{change:t.headImgChange}})])]):n("div",{staticClass:"wechat"},[n("img",{attrs:{width:"80%",src:t.wechat,alt:""}}),t._v(" "),n("button",{on:{click:t.clearCode}},[t._v("重新上传")])])])},staticRenderFns:[]};var d=a("VU/8")(o,r,!1,function(t){a("cN2N")},"data-v-cede7d26",null);e.default=d.exports},cN2N:function(t,e){}});
//# sourceMappingURL=18.9e651de803a6a1e1c5fc.js.map