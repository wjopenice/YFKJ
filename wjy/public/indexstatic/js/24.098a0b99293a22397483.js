webpackJsonp([24],{"00qC":function(n,o){},fKI4:function(n,o,e){"use strict";Object.defineProperty(o,"__esModule",{value:!0});var t=e("vMJZ"),i=e("4vvA"),a=e.n(i),r=(e("GKy3"),e("6xqC")),m=e.n(r),s=(e("MHRi"),e("Fuge")),c={name:"Opinion",data:function(){return{opinionForm:{content:"",email:""}}},methods:{submitOpinion:function(){var n=this;""==this.opinionForm.content||this.opinionForm.content.length<20?a()("描述不少于20个字"):this.opinionForm.content.length>200?a()("描述不超过200个字"):""!=this.opinionForm.email?Object(s.a)(this.opinionForm.email)?Object(t.p)(this.opinionForm).then(function(o){m.a.alert({message:"已成功提交, 处理后会尽快回复您!"}).then(function(){n.$router.replace("/mine")})}):a()("请填写正确邮箱"):a()("请填写邮箱")}}},l={render:function(){var n=this,o=n.$createElement,e=n._self._c||o;return e("div",{staticClass:"appbox"},[e("form",{attrs:{action:""}},[e("label",{attrs:{for:"problem"}},[n._v("问题和意见")]),n._v(" "),e("textarea",{directives:[{name:"model",rawName:"v-model",value:n.opinionForm.content,expression:"opinionForm.content"}],attrs:{id:"problem",name:"",placeholder:"具体说说您的问题，描述不少于20个字，我们会尽快为您解决。"},domProps:{value:n.opinionForm.content},on:{input:function(o){o.target.composing||n.$set(n.opinionForm,"content",o.target.value)}}}),n._v(" "),e("label",{attrs:{for:"contact"}},[n._v("您的联系方式")]),n._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:n.opinionForm.email,expression:"opinionForm.email"}],attrs:{id:"contact",type:"email",name:"",placeholder:"邮箱"},domProps:{value:n.opinionForm.email},on:{input:function(o){o.target.composing||n.$set(n.opinionForm,"email",o.target.value)}}})]),n._v(" "),e("button",{staticClass:"verify",on:{click:n.submitOpinion}},[n._v("提交")])])},staticRenderFns:[]};var p=e("VU/8")(c,l,!1,function(n){e("00qC")},"data-v-92ffce5e",null);o.default=p.exports}});
//# sourceMappingURL=24.098a0b99293a22397483.js.map