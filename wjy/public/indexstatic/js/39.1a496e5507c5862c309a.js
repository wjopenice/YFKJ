webpackJsonp([39],{z7eQ:function(e,n){},zV85:function(e,n,t){"use strict";Object.defineProperty(n,"__esModule",{value:!0});var a=t("Dd8w"),i=t.n(a),c=t("vMJZ"),o=t("4vvA"),s=t.n(o),r=(t("GKy3"),t("NYxO")),u={name:"Nickname",computed:i()({},Object(r.b)(["avatar","name","token"])),data:function(){return{nickname:""}},mounted:function(){this.nickname=this.name},methods:{setNickname:function(){var e=this;""!=this.nickname&&0!=this.nickname.length?Object(c.w)({nickname:this.nickname}).then(function(n){s()("设置成功"),e.$store.dispatch("user/setName",e.nickname).then(function(){e.$router.go(-1)}).catch(function(){})}):s()("请输入用户名")}}},m={render:function(){var e=this,n=e.$createElement,t=e._self._c||n;return t("div",{staticClass:"appbox"},[t("div",{staticClass:"inputBox"},[t("input",{directives:[{name:"model",rawName:"v-model",value:e.nickname,expression:"nickname"}],attrs:{type:"text",placeholder:"请输入名称"},domProps:{value:e.nickname},on:{input:function(n){n.target.composing||(e.nickname=n.target.value)}}})]),e._v(" "),t("button",{on:{click:e.setNickname}},[e._v("确认")])])},staticRenderFns:[]};var d=t("VU/8")(u,m,!1,function(e){t("z7eQ")},"data-v-19ef296e",null);n.default=d.exports}});
//# sourceMappingURL=39.1a496e5507c5862c309a.js.map