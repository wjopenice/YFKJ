webpackJsonp([36],{dV5u:function(t,s){},nOLk:function(t,s,e){"use strict";Object.defineProperty(s,"__esModule",{value:!0});var a=e("bOdI"),i=e.n(a),n=e("gyMJ"),r=(e("4vvA"),e("GKy3"),e("Fd2+")),c=(e("Mcfu"),{name:"Searchred",components:i()({},r.b.name,r.b),data:function(){return{loading:!1,finished:!1,page:1,room_number:"",currency_id:"",listData:[]}},mounted:function(){this.currency_id=this.$route.query.currencyId,this.getList()},methods:{getList:function(){var t=this;this.loading=!0,Object(n.g)({currency_id:this.currency_id,room_number:this.room_number,page:this.page}).then(function(s){t.loading=!1,t.page++,s.length<20&&(t.finished=!0),t.listData=t.listData.concat(s)})}}}),l={render:function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"app"},[t.listData.length?a("van-list",{attrs:{finished:t.finished,"finished-text":"没有更多了~"},on:{load:t.getList},model:{value:t.loading,callback:function(s){t.loading=s},expression:"loading"}},[a("ul",{staticClass:"moneyList"},t._l(t.listData,function(s,e){return a("li",[a("router-link",{staticClass:"ov",attrs:{to:{path:"/redchat",query:{groupId:s.id,type:"all",currency:s.currency_id,order:e+1}}}},[a("div",{staticClass:"fl headImg"},[a("img",{attrs:{src:s.currency.icon,alt:""}})]),t._v(" "),a("div",{staticClass:"fl infoBox"},[a("p",{staticClass:"nameBox"},[a("span",{staticClass:"name"},[t._v(t._s(s.money)+"个币"+t._s(s.count)+"个包")])]),t._v(" "),a("p",{staticClass:"time"},[t._v(t._s(1==s.send_rule?"最小":"最大")+"的发")])]),t._v(" "),a("div",{staticClass:"fr eosNumber"},[a("p",{staticClass:"number"},[t._v("成员数"+t._s(s.user_count))]),t._v(" "),a("p",{staticClass:"less"},[t._v("加入")])])])],1)}),0)]):t._e(),t._v(" "),t.listData.length?t._e():a("div",{staticClass:"searchresult"},[a("img",{attrs:{src:e("PUzD"),alt:""}}),t._v(" "),a("p",[t._v("空空如也")])])],1)},staticRenderFns:[]};var o=e("VU/8")(c,l,!1,function(t){e("dV5u")},"data-v-318e4b3e",null);s.default=o.exports}});
//# sourceMappingURL=36.f815f66d25ebc47c07ea.js.map