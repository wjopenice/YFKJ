webpackJsonp([43],{"+ed2":function(t,e){},"1kbH":function(t,e){},"2s7n":function(t,e){},"77xd":function(t,e){},"9IIc":function(t,e){},"9S6h":function(t,e){},Fw7Z:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var r=n("Dd8w"),a=n.n(r),i=(n("Av7u"),n("gyMJ")),o=n("NYxO"),s=n("JOqv"),c=n("4vvA"),u=n.n(c),l=(n("GKy3"),n("7+uW")),d=n("Fd2+"),m=(n("08XL"),n("G/J0"),n("6xqC")),p=n.n(m);n("MHRi");l.default.use(d.c).use(d.d);var f={components:{tabBar:s.a},computed:a()({},Object(o.b)(["avatar","name","token"])),name:"Index",data:function(){return{currency:{},currency_id:"",treeNumber:"",adData:[],currencyData:[],redList:[]}},mounted:function(){this.currency_id=this.$route.query.currencyId,this.currency_id=this.currency_id?this.currency_id:1,this.getCurrencyInfo(),this.getAdList(),this.getHomeList()},methods:{searchTree:function(){if(""==this.treeNumber||0==this.treeNumber.length)return u()("请输入财富树密码"),!1;this.$router.push({path:"/jointree",query:{number:this.treeNumber}})},getCurrencyInfo:function(){var t=this;Object(i.e)(this.currency_id).then(function(e){t.currency=e})},getHomeList:function(){var t=this;Object(i.h)(this.currency_id).then(function(e){t.currencyData=e.currency,t.redList=e.redList})},getAdList:function(){var t=this;Object(i.b)().then(function(e){t.adData=e})},goSearch:function(){this.$router.push("/searchred")},goAdDetail:function(t){1==t.type?this.$router.push("/adDetail?id="+t.id):this.$router.push("/cooperation?id="+t.id)},getReward:function(){Object(i.j)().then(function(t){1==t.reward&&p.a.alert({title:"首次登陆",message:t.message}).then(function(){})})},showAlert:function(){p.a.alert({title:"系统提醒",message:"当前币种暂未开放"}).then(function(){})}},activated:function(){this.name&&this.getReward()}},h={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("div",{staticClass:"app"},[n("div",{staticClass:"searchBox ov"},[n("div",{staticClass:"select fl"},[n("router-link",{attrs:{to:"/currency"}},[n("span",[t._v(t._s(t.currency.name))]),t._v(" "),n("i")])],1),t._v(" "),n("div",{staticClass:"search pr fr",on:{click:t.goSearch}},[n("input",{staticClass:"placeholder",attrs:{type:"text",placeholder:"请输入红包房间号",readonly:""}})])]),t._v(" "),n("van-swipe",{attrs:{autoplay:3e3,"indicator-color":"white"}},t._l(t.adData,function(e,r){return n("van-swipe-item",{key:r,on:{click:function(n){return t.goAdDetail(e)}}},[n("img",{staticStyle:{display:"block",width:"100%",height:"auto"},attrs:{src:e.pic,alt:""}})])}),1),t._v(" "),n("div",{staticClass:"listBox pr"},[n("div",{staticClass:"wealthTree"},[n("i"),t._v(" "),n("div",{staticClass:"wealthTreePwd ov"},[n("input",{directives:[{name:"model",rawName:"v-model",value:t.treeNumber,expression:"treeNumber"}],staticClass:"fl",attrs:{type:"text",placeholder:"请输入财富树密码"},domProps:{value:t.treeNumber},on:{input:function(e){e.target.composing||(t.treeNumber=e.target.value)}}}),n("button",{staticClass:"fr",on:{click:t.searchTree}},[t._v("确定")])])]),t._v(" "),n("div",{staticClass:"usdtTitle ov"},[n("p",{staticClass:"fl"},[t._v(t._s(t.currency.name)+"红包接龙")]),t._v(" "),n("router-link",{staticClass:"fr",attrs:{to:{path:"/currencyred",query:{currencyId:t.currency_id}}}},[t._v("更多")])],1),t._v(" "),n("ul",{staticClass:"listUl"},t._l(t.redList,function(e,r){return n("li",[n("router-link",{attrs:{to:{path:"/redchat",query:{groupId:e.id,type:"all",currency:e.currency_id,order:r+1}}}},[n("img",{attrs:{src:e.icon,alt:""}}),t._v(" "),n("span",[t._v(t._s(e.name))])])],1)}),0),t._v(" "),n("div",{staticClass:"usdtTitle ov hotTitle"},[n("p",{staticClass:"fl"},[t._v("热门币种区红包接龙")]),t._v(" "),n("router-link",{staticClass:"fr",attrs:{to:"/hotred"}},[t._v("更多")])],1),t._v(" "),n("ul",{staticClass:"listUl"},t._l(t.currencyData,function(e,r){return n("li",{key:r},[1==e.state?n("router-link",{attrs:{to:{path:"/currencyred",query:{currencyId:e.id}}}},[n("img",{attrs:{src:e.icon,alt:""}}),t._v(" "),n("span",[t._v(t._s(e.name))])]):n("a",{attrs:{href:"javascript:void (0)"},on:{click:t.showAlert}},[n("img",{attrs:{src:e.icon,alt:""}}),t._v(" "),n("span",[t._v(t._s(e.name))])])],1)}),0)])],1),t._v(" "),n("tab-bar")],1)},staticRenderFns:[]};var v=n("VU/8")(f,h,!1,function(t){n("2s7n")},"data-v-61e3c2d1",null);e.default=v.exports},IcnI:function(t,e,n){"use strict";var r=n("7+uW"),a=n("NYxO"),i={token:function(t){return t.user.token},avatar:function(t){return t.user.avatar},name:function(t){return t.user.name},wechat:function(t){return t.user.wechat}},o=n("//Fk"),s=n.n(o),c=n("vMJZ"),u=n("TIfe"),l={namespaced:!0,state:{token:Object(u.b)(),name:"",avatar:"",wechat:""},mutations:{SET_TOKEN:function(t,e){t.token=e},SET_NAME:function(t,e){t.name=e},SET_AVATAR:function(t,e){t.avatar=e},SET_WECHAT:function(t,e){t.wechat=e}},actions:{login:function(t,e){var n=t.commit,r=e.username,a=e.password;return new s.a(function(t,e){Object(c.i)({username:r.trim(),password:a}).then(function(e){var r=e;n("SET_TOKEN",r.token),Object(u.e)(r.token),t()}).catch(function(t){e(t)})})},getInfo:function(t){var e=t.commit,n=t.state;return new s.a(function(t,r){Object(c.f)(n.token).then(function(n){var a=n;a||r("Verification failed, please Login again.");var i=a.nickname,o=a.avatar,s=a.wechat_code;e("SET_NAME",i),e("SET_AVATAR",o),e("SET_WECHAT",s),t(a)}).catch(function(t){r(t)})})},logout:function(t){var e=t.commit,n=t.state;return new s.a(function(t,r){Object(c.j)(n.token).then(function(){e("SET_TOKEN",""),e("SET_NAME",""),e("SET_AVATAR",""),e("SET_WECHAT",""),Object(u.c)(),t()}).catch(function(t){r(t)})})},resetToken:function(t){var e=t.commit;return new s.a(function(t){e("SET_TOKEN",""),Object(u.c)(),t()})},setWechat:function(t,e){var n=t.commit;return new s.a(function(t){n("SET_WECHAT",e),t()})},setAvatar:function(t,e){var n=t.commit;return new s.a(function(t){n("SET_AVATAR",e),t()})},setName:function(t,e){var n=t.commit;return new s.a(function(t){n("SET_NAME",e),t()})}}};r.default.use(a.a);var d=new a.a.Store({modules:{app:app,user:l},getters:i});e.a=d},IxY9:function(t,e){},JOqv:function(t,e,n){"use strict";var r={render:function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"tabBar"},[r("ul",[r("router-link",{staticClass:"tabHome",attrs:{to:"/",tag:"li"}},[r("i"),r("p",[t._v("首页")])]),t._v(" "),r("router-link",{staticClass:"tabHb",attrs:{to:"/redgroup",tag:"li"}},[r("i"),r("p",[t._v("红包群")])]),t._v(" "),r("router-link",{staticClass:"tabAdd",attrs:{to:"",tag:"li"}},[r("i",{on:{click:function(e){t.showbar=!t.showbar}}})]),t._v(" "),r("router-link",{staticClass:"tabCash",attrs:{to:"/tree",tag:"li"}},[r("i"),r("p",[t._v("财富树")])]),t._v(" "),r("router-link",{staticClass:"tabMy",attrs:{to:"/mine",tag:"li"}},[r("i"),r("p",[t._v("我的")])])],1),t._v(" "),r("transition",{staticStyle:{"z-index":"99"},attrs:{name:"custom-classes-transition","enter-active-class":"animated slideInUp","leave-active-class":"animated fadeOutDown"}},[r("p",{directives:[{name:"show",rawName:"v-show",value:t.showbar,expression:"showbar"}],staticClass:"showHide"},[r("router-link",{attrs:{to:"/redcreate"}},[r("img",{attrs:{src:n("MPrQ"),alt:""}}),r("span",[t._v("创建红包群")])]),t._v(" "),r("router-link",{attrs:{to:"/treecreate"}},[r("img",{attrs:{src:n("cgMv"),alt:""}}),r("span",[t._v("创建财富树")])])],1)])],1)},staticRenderFns:[]};var a=n("VU/8")({name:"TabBar",data:function(){return{showbar:!1}},activated:function(){this.showbar=!1}},r,!1,function(t){n("eAge")},"data-v-491e5a94",null);e.a=a.exports},MPrQ:function(t,e,n){t.exports=n.p+"indexstatic/img/found_image_hongbao.d8c1df8.png"},NHnr:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var r=n("7+uW"),a={render:function(){var t=this.$createElement,e=this._self._c||t;return e("div",{attrs:{id:"app"}},[e("keep-alive",[this.$route.meta.keepAlive?e("router-view",{key:this.$route.fullPath}):this._e()],1),this._v(" "),this.$route.meta.keepAlive?this._e():e("router-view",{key:this.$route.fullPath})],1)},staticRenderFns:[]};var i=n("VU/8")({name:"App",data:function(){return{transitionName:"slide-left"}},watch:{$route:function(t,e){"/"==t.path?this.transitionName="slide-right":this.transitionName="slide-left"}}},a,!1,function(t){n("IxY9")},null,null).exports,o=n("/ocq"),s=(n("Fw7Z"),n("TIfe")),c=n("vMJZ"),u=n("4vvA"),l=n.n(u),d=(n("GKy3"),{name:"Login",data:function(){return{loginForm:{username:"",password:""},redirect:void 0}},watch:{$route:{handler:function(t){this.redirect=t.query&&t.query.redirect},immediate:!0}},mounted:function(){this.getUser()},methods:{getUser:function(){var t=Object(s.a)();t=t.split("="),this.loginForm.username=t[0],this.loginForm.password=t[1]},login:function(){var t=this;return 0==this.loginForm.username.length?(l()("请填写用户名"),!1):0==this.loginForm.password.length?(l()("请填写密码"),!1):void this.$store.dispatch("user/login",this.loginForm).then(function(){var e=t.loginForm.username+"="+t.loginForm.password;Object(s.d)(e),t.$router.push({path:"/"})}).catch(function(){})},goback:function(){this.$router.go(-1)}}}),m={render:function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"appBox"},[r("div",{staticClass:"signin"},[r("img",{staticClass:"logo",attrs:{src:n("RBBf"),alt:""}}),t._v(" "),r("p",{staticClass:"title"},[t._v("MillionCoin")]),t._v(" "),r("ul",{staticClass:"list"},[r("li",[r("img",{attrs:{src:n("cDjA"),alt:""}}),t._v(" "),r("input",{directives:[{name:"model",rawName:"v-model",value:t.loginForm.username,expression:"loginForm.username"}],attrs:{type:"text",placeholder:"请输入用户名"},domProps:{value:t.loginForm.username},on:{input:function(e){e.target.composing||t.$set(t.loginForm,"username",e.target.value)}}})]),t._v(" "),r("li",[r("img",{attrs:{src:n("Vjz+"),alt:""}}),t._v(" "),r("input",{directives:[{name:"model",rawName:"v-model",value:t.loginForm.password,expression:"loginForm.password"}],attrs:{type:"password",placeholder:"请输入密码"},domProps:{value:t.loginForm.password},on:{input:function(e){e.target.composing||t.$set(t.loginForm,"password",e.target.value)}}})])]),t._v(" "),r("div",{staticClass:"login_box"},[r("button",{on:{click:t.login}},[t._v("登录")])]),t._v(" "),"rsuccess"!==t.redirect?r("div",{staticClass:"fast_reg"},[r("router-link",{attrs:{to:"/register"}},[t._v("快速注册")])],1):t._e(),t._v(" "),"rsuccess"==t.redirect?r("div",{staticClass:"fast_reg_back",on:{click:t.goback}},[t._v("返回上一步备份账号和密码")]):t._e()])])},staticRenderFns:[]};var p=n("VU/8")(d,m,!1,function(t){n("9IIc")},"data-v-5f5035c4",null).exports,f=(n("zpCd"),{name:"Register",data:function(){return{inviteCode:"",readState:!1}},mounted:function(){this.inviteCode=this.$route.query.code?this.$route.query.code:"",this.readState=!!this.inviteCode},methods:{register:function(){var t=this;0!=this.inviteCode.length&&""!=this.inviteCode||l()("请填写邀请码"),Object(c.v)({inviteCode:this.inviteCode}).then(function(e){t.$router.push({path:"rsuccess",query:{username:e.username,password:e.password}})})}}}),h={render:function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"signin"},[r("img",{staticClass:"logo",attrs:{src:n("RBBf"),alt:""}}),t._v(" "),r("p",{staticClass:"title"},[t._v("MillionCoin")]),t._v(" "),r("p",{staticClass:"code"},[t._v("邀请码")]),t._v(" "),r("ul",{staticClass:"list"},[r("li",[r("img",{attrs:{src:n("yeI2"),alt:""}}),t._v(" "),r("input",{directives:[{name:"model",rawName:"v-model",value:t.inviteCode,expression:"inviteCode"}],attrs:{readonly:t.readState,type:"text",placeholder:"请输入邀请码"},domProps:{value:t.inviteCode},on:{input:function(e){e.target.composing||(t.inviteCode=e.target.value)}}})])]),t._v(" "),r("div",{staticClass:"reg_box"},[r("button",{on:{click:t.register}},[t._v("立即注册")])])])},staticRenderFns:[]};var v=n("VU/8")(f,h,!1,function(t){n("QNxi")},"data-v-7c1e54fc",null).exports,g=n("TQvf"),A=n.n(g),b={name:"RSuccess",data:function(){return{username:"",password:""}},mounted:function(){this.username=this.$route.query.username,this.password=this.$route.query.password},methods:{goLogin:function(){this.$router.push({path:"/login?redirect=rsuccess"})},copy:function(t){var e=new A.a(".copy",{text:function(){return t}});e.on("success",function(t){l.a.success("复制成功"),e.destroy()}),e.on("error",function(t){l.a.fail("当前浏览器不支持"),e.destroy()})}}},w={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"appbox"},[n("div",{staticClass:"popup"},[n("p",{staticClass:"tip"},[t._v("请截图或者使用文本，妥善备份您的账号密码，账户一旦丢失，账户内的资产永久不可找回。")]),t._v(" "),n("div",{staticClass:"main"},[n("p",{staticClass:"top"},[t._v("您的账号")]),t._v(" "),n("p",{staticClass:"bottom",attrs:{id:"counterText"}},[t._v(t._s(t.username))]),t._v(" "),n("button",{staticClass:"copy",on:{click:function(e){return t.copy(t.username)}}},[t._v("复制账号")])]),t._v(" "),n("div",{staticClass:"main pwd"},[n("p",{staticClass:"top"},[t._v("您的密码")]),t._v(" "),n("p",{staticClass:"bottom",attrs:{id:"psdText"}},[t._v(t._s(t.password))]),t._v(" "),n("button",{staticClass:"copy",on:{click:function(e){return t.copy(t.password)}}},[t._v("复制密码")])])]),t._v(" "),n("button",{staticClass:"verify",on:{click:t.goLogin}},[t._v("我已经备份好，立刻验证")])])},staticRenderFns:[]};var C=n("VU/8")(b,w,!1,function(t){n("77xd")},"data-v-4fdf32a5",null).exports;n("dI0y");r.default.use(o.a);var y,k=new o.a({routes:[{path:"/",name:"Index",meta:{keepAlive:!0},component:function(){return new Promise(function(t){t()}).then(n.bind(null,"Fw7Z"))}},{path:"/adDetail",name:"AdDetail",component:function(){return n.e(22).then(n.bind(null,"A+qe"))}},{path:"/cooperation",name:"Cooperation",component:function(){return Promise.all([n.e(0),n.e(12)]).then(n.bind(null,"Eabe"))}},{path:"/currency",name:"Currency",component:function(){return n.e(20).then(n.bind(null,"nMZn"))}},{path:"/login",name:"Login",component:p},{path:"/register",name:"Register",component:v},{path:"/rsuccess",name:"RSuccess",component:C},{path:"/mine",name:"Mine",meta:{keepAlive:!0},component:function(){return new Promise(function(t){t()}).then(n.bind(null,"zpCd"))}},{path:"/introduction",name:"Introduction",meta:{keepAlive:!0},component:function(){return n.e(6).then(n.bind(null,"i+O2"))}},{path:"/wcurrency",name:"Wcurrency",component:function(){return new Promise(function(t){t()}).then(n.bind(null,"dI0y"))}},{path:"/recharge",name:"Recharge",component:function(){return n.e(16).then(n.bind(null,"ThX1"))}},{path:"/rechargelog",name:"Rechargelog",component:function(){return Promise.all([n.e(0),n.e(23)]).then(n.bind(null,"VLID"))}},{path:"/withdraw",name:"Withdraw",component:function(){return Promise.all([n.e(0),n.e(35)]).then(n.bind(null,"e2li"))}},{path:"/redindex",name:"Redindex",component:function(){return Promise.all([n.e(0),n.e(2)]).then(n.bind(null,"k8E7"))}},{path:"/invite",name:"Invite",component:function(){return n.e(19).then(n.bind(null,"3VcI"))}},{path:"/backups",name:"Backups",component:function(){return n.e(27).then(n.bind(null,"lil+"))}},{path:"/opinion",name:"Opinion",component:function(){return n.e(24).then(n.bind(null,"fKI4"))}},{path:"/userinfo",name:"Userinfo",component:function(){return n.e(25).then(n.bind(null,"UTVy"))}},{path:"/nickname",name:"Nickname",component:function(){return n.e(39).then(n.bind(null,"zV85"))}},{path:"/ruleNotice",name:"RuleNotice",component:function(){return n.e(40).then(n.bind(null,"yU4p"))}},{path:"/mrule",name:"Mrule",component:function(){return Promise.all([n.e(0),n.e(7)]).then(n.bind(null,"UeeR"))}},{path:"/mnotice",name:"Mnotice",component:function(){return Promise.all([n.e(0),n.e(8)]).then(n.bind(null,"RHss"))}},{path:"/noticeInfo",name:"NoticeInfo",component:function(){return n.e(29).then(n.bind(null,"ntYJ"))}},{path:"/wechatcode",name:"Wechatcode",component:function(){return n.e(18).then(n.bind(null,"8gPX"))}},{path:"/tree",name:"treeindex",meta:{keepAlive:!0},component:function(){return Promise.all([n.e(0),n.e(15)]).then(n.bind(null,"i7w9"))}},{path:"/treestrategy",name:"Treestrategy",component:function(){return Promise.all([n.e(0),n.e(28)]).then(n.bind(null,"Nwlf"))}},{path:"/treefinance",name:"Treefinance",component:function(){return Promise.all([n.e(0),n.e(41)]).then(n.bind(null,"77SZ"))}},{path:"/treeinfo",name:"Treeinfo",component:function(){return Promise.all([n.e(0),n.e(3)]).then(n.bind(null,"Rprb"))}},{path:"/treeinvite",name:"Treeinvite",component:function(){return Promise.all([n.e(0),n.e(14)]).then(n.bind(null,"y76A"))}},{path:"/treeteam",name:"Treeteam",component:function(){return Promise.all([n.e(0),n.e(21)]).then(n.bind(null,"ljnF"))}},{path:"/treeupgrade",name:"Treeupgrade",component:function(){return Promise.all([n.e(0),n.e(32)]).then(n.bind(null,"I6re"))}},{path:"/jointree",name:"Jointree",component:function(){return Promise.all([n.e(0),n.e(1)]).then(n.bind(null,"feW7"))}},{path:"/treecreate",name:"Treecreate",component:function(){return Promise.all([n.e(0),n.e(13)]).then(n.bind(null,"nx0M"))}},{path:"/redgroup",name:"Redgroup",meta:{keepAlive:!0},component:function(){return Promise.all([n.e(0),n.e(10)]).then(n.bind(null,"kDUW"))}},{path:"/redcreate",name:"Redcreate",component:function(){return Promise.all([n.e(0),n.e(26)]).then(n.bind(null,"1fxw"))}},{path:"/redstrategy",name:"Redstrategy",component:function(){return Promise.all([n.e(0),n.e(38)]).then(n.bind(null,"Ec/v"))}},{path:"/redchat",name:"Redchat",component:function(){return Promise.all([n.e(0),n.e(4)]).then(n.bind(null,"fDM5"))}},{path:"/redshare",name:"Redshare",component:function(){return Promise.all([n.e(0),n.e(5)]).then(n.bind(null,"+R4a"))}},{path:"/reddetail",name:"Reddetail",component:function(){return Promise.all([n.e(0),n.e(17)]).then(n.bind(null,"kzaL"))}},{path:"/usergroup",name:"Usergroup",component:function(){return Promise.all([n.e(0),n.e(11)]).then(n.bind(null,"bRnR"))}},{path:"/redpacketlog",name:"Redpacketlog",component:function(){return Promise.all([n.e(0),n.e(33)]).then(n.bind(null,"Jm5h"))}},{path:"/groupnotice",name:"Rroupnotice",component:function(){return Promise.all([n.e(0),n.e(30)]).then(n.bind(null,"xbL3"))}},{path:"/grouprule",name:"Rrouprule",component:function(){return Promise.all([n.e(0),n.e(34)]).then(n.bind(null,"dQNM"))}},{path:"/searchred",name:"Searchred",component:function(){return Promise.all([n.e(0),n.e(37)]).then(n.bind(null,"oCou"))}},{path:"/currencyred",name:"Currencyred",component:function(){return Promise.all([n.e(0),n.e(36)]).then(n.bind(null,"nOLk"))}},{path:"/hotred",name:"Hotred",component:function(){return Promise.all([n.e(0),n.e(31)]).then(n.bind(null,"2Imn"))}},{path:"/treelog",name:"Treelog",component:function(){return Promise.all([n.e(0),n.e(9)]).then(n.bind(null,"twUD"))}}]}),j=n("IcnI"),O=(n("uYw/"),n("Xxa5")),_=n.n(O),x=n("exGp"),E=n.n(x),D=this,N=["/","/login","/adDetail","/register","/rsuccess","/mine"];k.beforeEach((y=E()(_.a.mark(function t(e,n,r){return _.a.wrap(function(t){for(;;)switch(t.prev=t.next){case 0:if(!Object(s.b)()){t.next=24;break}if("/login"!==e.path){t.next=6;break}r({path:"/"}),t.next=22;break;case 6:if(!j.a.getters.name){t.next=11;break}r(),t.next=22;break;case 11:return t.prev=11,t.next=14,j.a.dispatch("user/getInfo");case 14:r(),t.next=22;break;case 17:return t.prev=17,t.t0=t.catch(11),t.next=21,j.a.dispatch("user/resetToken");case 21:r("/login?redirect="+e.path);case 22:t.next=25;break;case 24:-1!==N.indexOf(e.path)?r():r("/login?redirect="+e.path);case 25:case"end":return t.stop()}},t,D,[[11,17]])})),function(t,e,n){return y.apply(this,arguments)})),k.afterEach(function(){}),r.default.config.productionTip=!1,new r.default({el:"#app",store:j.a,router:k,components:{App:i},template:"<App/>"})},PjB5:function(t,e){},QNxi:function(t,e){},RBBf:function(t,e,n){t.exports=n.p+"indexstatic/img/caifushukaiqi_logo.3eece75.png"},TIfe:function(t,e,n){"use strict";e.b=function(){return a.a.get(i)},e.e=function(t){return a.a.set(i,t)},e.c=function(){return a.a.remove(i)},e.a=function(){return a.a.get(o)},e.d=function(t){return a.a.set(o,t)};var r=n("lbHh"),a=n.n(r),i="vue_wjy_token",o="vue_wjy_app_login"},"Uf+m":function(t,e){},"Vjz+":function(t,e){t.exports="data:image/png;base64,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"},XqYu:function(t,e){},YAYC:function(t,e){},aG1J:function(t,e){},cDjA:function(t,e){t.exports="data:image/png;base64,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"},cgMv:function(t,e,n){t.exports=n.p+"indexstatic/img/found_image_tree.c45053d.png"},dI0y:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var r=n("vMJZ"),a=n("6xqC"),i=n.n(a),o=(n("MHRi"),{name:"Wcurrency",data:function(){return{keyword:"",listData:[]}},mounted:function(){this.getList()},methods:{getList:function(){var t=this;Object(r.D)(this.keyword).then(function(e){t.listData=e})},showAlert:function(){i.a.alert({title:"系统提醒",message:"当前币种暂未开放"}).then(function(){})}}}),s={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"app"},[n("div",{staticClass:"searchBox"},[n("div",{staticClass:"searchInputBox"},[n("i"),t._v(" "),n("input",{directives:[{name:"model",rawName:"v-model",value:t.keyword,expression:"keyword"}],attrs:{type:"text",placeholder:"搜索"},domProps:{value:t.keyword},on:{blur:t.getList,input:function(e){e.target.composing||(t.keyword=e.target.value)}}})])]),t._v(" "),n("ul",{staticClass:"curList"},t._l(t.listData,function(e){return n("li",[1==e.state?n("router-link",{attrs:{to:{path:"/recharge",query:{currencyId:e.id}}}},[n("img",{attrs:{src:e.icon,alt:""}}),t._v(" "),n("p",[n("span",[t._v(t._s(e.name))]),t._v(" "),n("span",[t._v(t._s(e.tag))])]),t._v(" "),n("span",{staticClass:"curNum"},[t._v(t._s(e.balance))])]):n("a",{attrs:{href:"javascript:void (0)"},on:{click:t.showAlert}},[n("img",{attrs:{src:e.icon,alt:""}}),t._v(" "),n("p",[n("span",[t._v(t._s(e.name))]),t._v(" "),n("span",[t._v(t._s(e.tag))])]),t._v(" "),n("span",{staticClass:"curNum"},[t._v(t._s(e.balance))])])],1)}),0),t._v(" "),n("router-link",{staticClass:"cooperation",attrs:{to:"/cooperation",href:" "}},[n("p",[n("span",[t._v("项目方代币上线申请")])])])],1)},staticRenderFns:[]};var c=n("VU/8")(o,s,!1,function(t){n("PjB5")},"data-v-058347f0",null);e.default=c.exports},eAge:function(t,e){},gyMJ:function(t,e,n){"use strict";e.f=function(t){return Object(r.a)({url:"/index/currencyList",method:"get",params:{keyword:t}})},e.d=function(){return Object(r.a)({url:"/index/currencyFirst",method:"get"})},e.e=function(t){return Object(r.a)({url:"/index/currencyInfo",method:"get",params:{currency_id:t}})},e.h=function(t){return Object(r.a)({url:"/index/homeList",method:"get",params:{currency_id:t}})},e.k=function(t){return Object(r.a)({url:"/index/searchRed",method:"get",params:{room_number:t}})},e.g=function(t){return Object(r.a)({url:"/index/currencyRed",method:"get",params:t})},e.i=function(t){return Object(r.a)({url:"/index/hotRed",method:"get",params:t})},e.b=function(t){return Object(r.a)({url:"/index/adList",method:"get",params:t})},e.a=function(t){return Object(r.a)({url:"/index/adDetail",method:"get",params:t})},e.j=function(t){return Object(r.a)({url:"/index/reward",method:"get",params:t})},e.c=function(t){return Object(r.a)({url:"/index/cooperation",method:"post",data:t})};var r=n("vLgD")},rCCi:function(t,e,n){t.exports=n.p+"indexstatic/img/per_image_default.e0e093f.png"},s1Ps:function(t,e){},"uYw/":function(t,e){},vLgD:function(t,e,n){"use strict";var r=n("//Fk"),a=n.n(r),i=n("mtWM"),o=n.n(i),s=n("IcnI"),c=n("TIfe"),u=n("BO1k"),l=n.n(u),d=n("Av7u"),m=n.n(d);var p=n("6xqC"),f=n.n(p),h=(n("MHRi"),n("4vvA")),v=n.n(h),g=(n("GKy3"),o.a.create({baseURL:"api",withCredentials:!0,timeout:5e3}));g.interceptors.request.use(function(t){return s.a.getters.token&&(t.headers.token=Object(c.b)()),t.headers.sign=function(t){var e="",n=[];t.params?n=t.params:t.data&&(n=t.data);var r=[];for(var a in n)r.push(a);var i=r.sort(),o=!0,s=!1,c=void 0;try{for(var u,d=l()(i);!(o=(u=d.next()).done);o=!0){var p=u.value;e+=p+"="+n[p]+"&"}}catch(t){s=!0,c=t}finally{try{!o&&d.return&&d.return()}finally{if(s)throw c}}return e=e.slice(0,-1),m.a.HmacSHA256(e,"wjyappkey").toString()}(t),t},function(t){return console.log(t),a.a.reject(t)}),g.interceptors.response.use(function(t){var e=t.data;return e?0===e.status?a.a.reject(new Error(e.msg||"Error")):10001===e.status?(v.a.clear(),f.a.alert({title:"系统提醒",message:"当前未登录或登录状态已过期, 是否立即登录"}).then(function(){s.a.dispatch("user/resetToken").then(function(){location.reload()})}),a.a.reject(new Error("登录失效"))):20001===e.status?(v.a.clear(),v()(e.msg||"操作失败"),a.a.reject()):30001===e.status?(v.a.clear(),f.a.alert({title:"系统提醒",message:e.msg}).then(function(){}),a.a.reject()):1===e.status?e.data:a.a.reject(new Error("debug:"+e)):a.a.reject(new Error("啥都没有"))},function(t){return console.log("err"+t),a.a.reject(t)});e.a=g},vMJZ:function(t,e,n){"use strict";e.v=function(t){return Object(r.a)({url:"/user/register",method:"post",data:t})},e.i=function(t){return Object(r.a)({url:"/user/login",method:"post",data:t})},e.f=function(t){return Object(r.a)({url:"/user/getUserInfo",method:"get",params:{token:t}})},e.j=function(){return Object(r.a)({url:"/user/logout",method:"post"})},e.D=function(t){return Object(r.a)({url:"/user/wallet",method:"get",params:{keyword:t}})},e.e=function(t){return Object(r.a)({url:"/user/currencyInfo",method:"get",params:{currency_id:t}})},e.c=function(t){return Object(r.a)({url:"/user/cashInfo",method:"get",params:{currency_id:t}})},e.b=function(t){return Object(r.a)({url:"/user/cash",method:"post",data:t})},e.q=function(t){return Object(r.a)({url:"/user/rechargeLog",method:"get",params:t})},e.d=function(t){return Object(r.a)({url:"/user/cashLog",method:"get",params:t})},e.h=function(){return Object(r.a)({url:"/user/inviteInfo",method:"get"})},e.a=function(){return Object(r.a)({url:"/user/accountInfo",method:"get"})},e.p=function(t){return Object(r.a)({url:"/user/opinion",method:"post",data:t})},e.w=function(t){return Object(r.a)({url:"/user/setUserInfo",method:"post",data:t})},e.A=function(t){return Object(r.a)({url:"/user/avatar",method:"post",data:t})},e.B=function(t){return Object(r.a)({url:"/user/wechat",method:"post",data:t})},e.t=function(t){return Object(r.a)({url:"/user/redProfit",method:"get",params:{currency_id:t}})},e.g=function(t){return Object(r.a)({url:"/user/groupProfit",method:"get",params:t})},e.s=function(t){return Object(r.a)({url:"/user/redLog",method:"get",params:t})},e.r=function(t){return Object(r.a)({url:"/user/redDis",method:"get",params:t})},e.n=function(t){return Object(r.a)({url:"user/mycurrency",method:"get",params:t})},e.C=function(t){return Object(r.a)({url:"user/userInfo",method:"get",params:t})},e.x=function(t){return Object(r.a)({url:"user/treelog",method:"get",params:t})},e.u=function(t){return Object(r.a)({url:"user/reduser",method:"get",params:t})},e.y=function(t){return Object(r.a)({url:"user/treeuser",method:"get",params:t})},e.m=function(t){return Object(r.a)({url:"/user/ruleNotice",method:"get",params:t})},e.l=function(t){return Object(r.a)({url:"/user/ruleNotice",method:"get",params:t})},e.o=function(t){return Object(r.a)({url:"/user/noticeInfo",method:"get",params:t})},e.k=function(t){return Object(r.a)({url:"/user/lookNotice",method:"get",params:t})},e.z=function(t){return Object(r.a)({url:"/user/upLookNotice",method:"get",params:t})};var r=n("vLgD")},yeI2:function(t,e){t.exports="data:image/png;base64,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"},zpCd:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var r=n("Dd8w"),a=n.n(r),i=n("JOqv"),o=n("NYxO"),s=n("vMJZ"),c={components:{tabBar:i.a},computed:a()({},Object(o.b)(["avatar","name","token"])),name:"Mine",data:function(){return{defaultAvatr:n("rCCi"),unlook:0}},methods:{goLogin:function(){this.$router.push({path:"/login?redirect=mine"})},goUserInfo:function(){this.$router.push({path:"/userinfo"})},logout:function(){this.$store.dispatch("user/logout").then(function(){}).catch(function(){})},getLookNotice:function(){var t=this;this.name&&Object(s.k)().then(function(e){t.unlook=e.unlook})}},activated:function(){this.getLookNotice()}},u={render:function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",[r("div",{staticClass:"app"},[r("div",{staticClass:"myHead pr"},[r("div",{staticClass:"imgBox"},[t.token?r("img",{attrs:{src:t.avatar,alt:""},on:{click:t.goUserInfo}}):r("img",{attrs:{src:n("rCCi"),alt:""},on:{click:t.goLogin}}),t._v(" "),t.token?r("span",{staticStyle:{color:"#fff"},on:{click:t.goUserInfo}},[t._v(t._s(t.name)),r("i",{staticStyle:{color:"rgb(210, 210, 210)","font-size":".12rem"}},[t._v("(点击修改)")])]):r("span",{on:{click:t.goLogin}},[t._v("登录后体验更多服务")])])]),t._v(" "),r("ul",{staticClass:"myList"},[r("li",[r("router-link",{attrs:{to:"/introduction"}},[r("i",{staticClass:"pingtai"}),r("p",[t._v("平台简介")]),r("span")])],1),t._v(" "),r("li",[r("router-link",{attrs:{to:"/wcurrency"}},[r("i",{staticClass:"zijin"}),r("p",[t._v("个人资金")]),r("span")])],1),t._v(" "),r("li",[r("router-link",{attrs:{to:"/redindex"}},[r("i",{staticClass:"hbsy"}),r("p",[t._v("红包总收益")]),r("span")])],1),t._v(" "),r("li",[r("router-link",{attrs:{to:"/treelog"}},[r("i",{staticClass:"cfsy"}),r("p",[t._v("财富树总收益")]),r("span")])],1),t._v(" "),r("li",[r("router-link",{attrs:{to:"/backups"}},[r("i",{staticClass:"pwd"}),r("p",[t._v("备份账户密码")]),r("span")])],1),t._v(" "),r("li",[r("router-link",{attrs:{to:"/wechatcode"}},[r("i",{staticClass:"wxcode"}),r("p",[t._v("上传微信二维码")]),r("span")])],1),t._v(" "),r("li",[r("router-link",{attrs:{to:"/invite"}},[r("i",{staticClass:"friend"}),r("p",[t._v("邀请好友")]),r("span")])],1),t._v(" "),r("li",[r("router-link",{attrs:{to:"/opinion"}},[r("i",{staticClass:"feedback"}),r("p",[t._v("意见反馈")]),r("span")])],1),t._v(" "),r("li",[r("router-link",{attrs:{to:"/mnotice"}},[r("i",{staticClass:"notice"}),r("p",[t._v("公告")]),t.unlook>0?r("div",{staticClass:"point"}):t._e(),r("span")])],1)]),t._v(" "),t.token?r("div",{staticClass:"signOutBox"},[r("button",{on:{click:t.logout}},[t._v("退出登录")])]):t._e()]),t._v(" "),r("tab-bar")],1)},staticRenderFns:[]};var l=n("VU/8")(c,u,!1,function(t){n("1kbH")},"data-v-778c6c80",null);e.default=l.exports}},["NHnr"]);
//# sourceMappingURL=app.476fe2bdb859d8f73580.js.map