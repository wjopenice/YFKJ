webpackJsonp([32],{I6re:function(e,t,s){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n=s("8amI"),l=s("cnhE"),a=s("4vvA"),o=s.n(a),r=(s("GKy3"),s("6xqC")),v=s.n(r),i=(s("MHRi"),s("VvTn")),c=s.n(i),f={name:"Treeupgrade",components:{treeBar:n.a},data:function(){return{tree_id:"",treeInfo:{},userInfo:{},tolevel:0,upgradMoney:0}},watch:{tolevel:function(e){this.tolevel>this.treeInfo.level&&(this.tolevel=this.treeInfo.level);for(var t=0,s=0;s<this.tolevel;s++)s<this.userInfo.vip_level||(t=c.a.floatAdd(t,this.treeInfo.upgrade_money[s]));this.upgradMoney=t}},created:function(){this.tree_id=this.$route.query.treeId},mounted:function(){this.getUpgradeInfo()},methods:{getUpgradeInfo:function(){var e=this;Object(l.k)({tree_id:this.tree_id}).then(function(t){e.userInfo=t.userInfo,e.treeInfo=t.tree,e.userInfo.vip_level<e.treeInfo.level&&(e.tolevel=e.userInfo.vip_level+1)})},addLevel:function(){this.tolevel<this.treeInfo.level?this.tolevel++:o()("已是最高级别")},upgrade:function(){var e=this;v.a.confirm({title:"系统提醒",message:"升级到"+this.tolevel+"级将消耗"+this.upgradMoney+this.treeInfo.currency_name+", 确定升级么?"}).then(function(){Object(l.j)({tree_id:e.tree_id,tolevel:e.tolevel}).then(function(t){e.userInfo.vip_level=e.tolevel,e.tolevel=e.tolevel+1,e.userInfo.money=t.money})}).catch(function(){})}}},_={render:function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("div",{staticClass:"appBox"},[s("div",{staticClass:"bg"}),e._v(" "),s("p",{staticClass:"grade"},[e._v("您当前的级别: "+e._s(e.userInfo.vip_level)+"级")]),e._v(" "),s("div",{staticClass:"balanceBox"},[s("p",{staticClass:"usdtBalance"},[e._v(e._s(e.treeInfo.currency_name)+"余额："+e._s(e.userInfo.money))]),e._v(" "),s("p",{staticClass:"state"},[e._v("升级到")]),e._v(" "),s("div",{staticClass:"stateBox ov"},[s("p",{staticClass:"fl pr"},[s("span",{staticClass:"line"}),e._v(" "),s("span",{staticClass:"vg1"},[e._v("v"+e._s(e.userInfo.vip_level))]),e._v(" "),e.userInfo.vip_level<e.treeInfo.level?s("span",{staticClass:"vg2"},[e._v("v"+e._s(e.tolevel))]):s("span",{staticClass:"vg2"},[e._v("v"+e._s(e.treeInfo.level))])]),e._v(" "),s("i",{staticClass:"fl",on:{click:e.addLevel}})]),e._v(" "),e.userInfo.vip_level<e.treeInfo.level?s("div",{staticClass:"fast"},[s("span",[e._v("升到"+e._s(e.tolevel)+"级需要缴纳："+e._s(e.upgradMoney)+e._s(e.treeInfo.currency_name)+" ")]),e._v(" "),s("p",{on:{click:e.upgrade}},[e._v("立即升级")])]):s("div",{staticClass:"fast"},[s("p",[e._v("已满级")])])]),e._v(" "),s("tree-bar",{attrs:{tree_id:e.tree_id}})],1)},staticRenderFns:[]};var u=s("VU/8")(f,_,!1,function(e){s("KC6b")},"data-v-41a3ddc8",null);t.default=u.exports},KC6b:function(e,t){}});
//# sourceMappingURL=32.0d8d2336ffc7845b9da2.js.map