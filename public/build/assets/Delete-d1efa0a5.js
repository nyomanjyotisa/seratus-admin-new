import{u as f,g,a as c,w as m,o as _,d as t,t as o,e as n,b as l,n as b,f as h}from"./app-26ecd550.js";import{_ as y}from"./DangerButton-8cef36c0.js";import{_ as w,a as k}from"./SecondaryButton-8f0495a8.js";const x={class:"space-y-6"},v=["onSubmit"],C={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},S={class:"mt-1 text-sm text-gray-600 dark:text-gray-400"},$={class:"mt-6 flex justify-end"},D={__name:"Delete",props:{show:Boolean,permission:Object},emits:["close"],setup(u,{emit:r}){const i=u,s=f({}),p=()=>{var e;s.delete(route("permission.destroy",(e=i.permission)==null?void 0:e.id),{preserveScroll:!0,onSuccess:()=>{r("close"),s.reset()},onError:()=>null,onFinish:()=>null})};return(e,a)=>(_(),g("section",x,[c(w,{show:i.show,onClose:a[1]||(a[1]=d=>r("close")),maxWidth:"lg"},{default:m(()=>{var d;return[t("form",{class:"p-6",onSubmit:h(p,["prevent"])},[t("h2",C,o(e.lang().label.delete)+" "+o(e.lang().label.permission),1),t("p",S,[n(o(e.lang().label.delete_confirm)+" ",1),t("b",null,o((d=i.permission)==null?void 0:d.name),1),n("? ")]),t("div",$,[c(k,{disabled:l(s).processing,onClick:a[0]||(a[0]=B=>r("close"))},{default:m(()=>[n(o(e.lang().button.close),1)]),_:1},8,["disabled"]),c(y,{class:b(["ml-3",{"opacity-25":l(s).processing}]),disabled:l(s).processing,onClick:p},{default:m(()=>[n(o(l(s).processing?e.lang().button.delete+"...":e.lang().button.delete),1)]),_:1},8,["class","disabled"])])],40,v)]}),_:1},8,["show"])]))}};export{D as default};