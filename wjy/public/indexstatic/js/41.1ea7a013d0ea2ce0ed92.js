webpackJsonp([41],{"77SZ":function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var s=e("bOdI"),i=e.n(s),n=e("JOqv"),l=e("8amI"),r=e("cnhE"),c=(e("4vvA"),e("GKy3"),e("Fd2+")),o=(e("Mcfu"),{name:"Treefinance",components:i()({tabBar:n.a,treeBar:l.a},c.b.name,c.b),data:function(){return{tree_id:"",loading:!1,finished:!1,page:1,listData:[],headData:{income:0,expense:0}}},created:function(){this.tree_id=this.$route.query.treeId},mounted:function(){},methods:{getList:function(){var t=this;this.loading=!0,Object(r.g)({page:this.page,tree_id:this.tree_id}).then(function(a){t.loading=!1,1==t.page&&(t.headData=a.head),t.page++,a.list.length<20&&(t.finished=!0),t.listData=t.listData.concat(a.list)})}}}),d={render:function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",{staticClass:"app"},[e("div",{staticClass:"incomeBox"},[e("div",{staticClass:"titleInfo"},[e("div",{staticClass:"bottomBox ov"},[e("div",{staticClass:"fl left"},[e("p",[t._v("总共支付升级费用")]),e("span",[t._v(t._s(t.headData.expense))])]),t._v(" "),e("div",{staticClass:"fl right"},[e("p",[t._v("总共收取下级升级费用")]),e("span",[t._v(t._s(t.headData.income))])])])]),t._v(" "),t._m(0)]),t._v(" "),e("van-list",{attrs:{finished:t.finished,"finished-text":""},on:{load:t.getList},model:{value:t.loading,callback:function(a){t.loading=a},expression:"loading"}},[e("ul",{staticClass:"flowBox"},t._l(t.listData,function(a){return e("li",[e("a",{staticClass:"ov",attrs:{href:"javascript:void(0);"}},[e("p",{staticClass:"fl ov"},[e("img",{staticClass:"fl",attrs:{src:a.userinfo.avatar,alt:""}}),e("em",{staticClass:"fl"},[e("span",[t._v(t._s(a.userinfo.nickname))]),e("i",{staticClass:"time"},[t._v(t._s(a.datetime))])])]),t._v(" "),e("p",{staticClass:"fr"},[e("span",[t._v(t._s(a.money))]),e("i",[t._v(t._s(a.remark))])])])])}),0)]),t._v(" "),e("tree-bar",{attrs:{tree_id:t.tree_id}})],1)},staticRenderFns:[function(){var t=this.$createElement,a=this._self._c||t;return a("div",{staticClass:"holderNum pr"},[a("p",[this._v("财务流水记录")])])}]};var v=e("VU/8")(o,d,!1,function(t){e("sZi7")},"data-v-0c00aff4",null);a.default=v.exports},sZi7:function(t,a){}});
//# sourceMappingURL=41.1ea7a013d0ea2ce0ed92.js.map