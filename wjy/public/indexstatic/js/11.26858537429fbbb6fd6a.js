webpackJsonp([11],{RyVB:function(t,a){},bRnR:function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var n=e("bOdI"),i=e.n(n),s=e("ETS5"),u=(e("4vvA"),e("GKy3"),e("Fd2+")),o=(e("Mcfu"),{name:"GroupList",components:i()({},u.b.name,u.b),props:["uid","type"],data:function(){return{page:1,listData:[],loading:!1,finished:!1}},mounted:function(){this.getList()},methods:{getList:function(){var t=this;this.loading=!0,Object(s.v)({uid:this.uid,page:this.page,type:this.type}).then(function(a){t.loading=!1,t.page++,a.length<20&&(t.finished=!0),t.listData=t.listData.concat(a)})}}}),r={render:function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",[e("ul",{staticClass:"myList"},[e("van-list",{attrs:{finished:t.finished,"finished-text":"没有更多了"},on:{load:t.getList},model:{value:t.loading,callback:function(a){t.loading=a},expression:"loading"}},t._l(t.listData,function(a,n){return e("li",[e("router-link",{attrs:{to:{path:"/redchat",query:{groupId:a.redgroup_id,order:n+1,fuid:t.uid,type:1==t.type?"like":"create"}}}},[e("p",[t._v(t._s(a.name)+"（"+t._s(a.user_count)+"）")]),e("span")])],1)}),0)],1)])},staticRenderFns:[]};var c=e("VU/8")(o,r,!1,function(t){e("RyVB")},"data-v-cc28466a",null).exports,d=e("vMJZ"),l=(e("eNeO"),{name:"Usergroup",components:{GroupList:c},data:function(){return{uid:"",navNum:1,userInfo:{nickname:"",avatar:""},collectionData:[],mygroupData:[]}},created:function(){this.uid=this.$route.query.uid},mounted:function(){this.getUserInfo()},methods:{getUserInfo:function(){var t=this;Object(d.C)({uid:this.uid}).then(function(a){t.userInfo=a})}}}),v={render:function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",{staticClass:"app"},[e("div",{staticClass:"myHead pr"},[e("div",{staticClass:"imgBox"},[e("img",{attrs:{src:t.userInfo.avatar,alt:""}}),t._v(" "),e("span",[t._v(t._s(t.userInfo.nickname))])])]),t._v(" "),e("ul",{staticClass:"ov nav"},[e("li",{staticClass:"fl"},[e("a",{staticClass:"pr",class:{active:1==t.navNum},attrs:{href:"javascript:void (0)"},on:{click:function(a){t.navNum=1}}},[t._v("ta喜欢的红包群"),e("span")])]),t._v(" "),e("li",{staticClass:"fl"},[e("a",{staticClass:"pr",class:{active:2==t.navNum},attrs:{href:"javascript:void (0)"},on:{click:function(a){t.navNum=2}}},[t._v("ta创建的红包群"),e("span")])])]),t._v(" "),e("group-list",{directives:[{name:"show",rawName:"v-show",value:1==t.navNum,expression:"navNum == 1"}],attrs:{uid:t.uid,type:"1"}}),t._v(" "),e("group-list",{directives:[{name:"show",rawName:"v-show",value:2==t.navNum,expression:"navNum == 2"}],attrs:{uid:t.uid,type:"2"}})],1)},staticRenderFns:[]};var f=e("VU/8")(l,v,!1,function(t){e("fo25")},"data-v-f78294bc",null);a.default=f.exports},eNeO:function(t,a,e){e("XqYu"),e("gMie")},fo25:function(t,a){},gMie:function(t,a){}});
//# sourceMappingURL=11.26858537429fbbb6fd6a.js.map