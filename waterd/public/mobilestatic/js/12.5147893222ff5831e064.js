webpackJsonp([12],{hEKC:function(t,n,e){"use strict";Object.defineProperty(n,"__esModule",{value:!0});var i=e("ArgV"),o={name:"Noticedetail",components:{navbar:e("J56h").a},data:function(){return{notice_id:"",notice:{}}},mounted:function(){this.notice_id=this.$route.query.noticeId,this.lookup()},methods:{lookup:function(){var t=this;Object(i.d)({notice_id:this.notice_id}).then(function(n){t.notice=n})}}},s={render:function(){var t=this.$createElement,n=this._self._c||t;return n("div",["Android"==this.$phonemodel?n("navbar",{attrs:{title:"公告详情"}}):this._e(),this._v(" "),n("div",{staticClass:"app",class:{nav:"Android"==this.$phonemodel}},[n("div",{domProps:{innerHTML:this._s(this.notice.content)}})])],1)},staticRenderFns:[]};var c=e("VU/8")(o,s,!1,function(t){e("yOUy")},"data-v-d99bf492",null);n.default=c.exports},yOUy:function(t,n){}});
//# sourceMappingURL=12.5147893222ff5831e064.js.map