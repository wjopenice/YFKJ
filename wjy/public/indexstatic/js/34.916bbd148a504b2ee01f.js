webpackJsonp([34],{dQNM:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=n("ETS5"),o=(n("TQvf"),n("4vvA")),r=n.n(o),c=(n("GKy3"),{name:"Groupnotice",data:function(){return{content:"",iscreate:!1}},mounted:function(){this.redgroup_id=this.$route.query.groupId,this.getInfo()},methods:{getInfo:function(){var t=this;Object(i.i)({redgroup_id:this.redgroup_id}).then(function(e){t.content=e.content,t.iscreate=e.iscreate})},setContent:function(){this.content.length>100?r()("规则内容最多为100个字符"):Object(i.t)({redgroup_id:this.redgroup_id,content:this.content}).then(function(t){r()("更新规则成功")})}}}),s={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"appBox"},[n("div",{staticClass:"text"},[n("textarea",{directives:[{name:"model",rawName:"v-model",value:t.content,expression:"content"}],attrs:{name:"",readonly:!t.iscreate,placeholder:"只有群主才能设置规则，字数不能大于100字符"},domProps:{value:t.content},on:{input:function(e){e.target.composing||(t.content=e.target.value)}}}),t._v(" "),t.iscreate?n("div",{staticClass:"button"},[n("button",{on:{click:t.setContent}},[t._v("提交")])]):t._e()]),t._v(" "),t._m(0)])},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("p",[e("i"),e("span",[this._v("仅群主可编辑")]),e("i")])}]};var a=n("VU/8")(c,s,!1,function(t){n("nfPi")},"data-v-36531c74",null);e.default=a.exports},nfPi:function(t,e){}});
//# sourceMappingURL=34.916bbd148a504b2ee01f.js.map