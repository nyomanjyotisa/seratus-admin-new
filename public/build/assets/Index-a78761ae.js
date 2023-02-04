import{O as j,J as I,z as V,R as g,S as B,f as m,a as r,u as d,w as f,F as x,o as i,X as D,b as s,j as c,v as _,d as w,t as n,c as k,E as P,C as N,D as U}from"./app-11bf830f.js";import{_ as E,a as A}from"./AuthenticatedLayout-52a44165.js";import{a as F}from"./TextInput-dc7b9220.js";import{_ as J}from"./PrimaryButton-f0788832.js";import{r as $,a as b,_ as z,b as G,c as L}from"./InfoButton-d0373a02.js";import{_ as M}from"./SelectInput-3e8a909b.js";import{_ as O}from"./DangerButton-41c18ab4.js";import R from"./Create-3f3ba78d.js";import T from"./Edit-87b916be.js";import X from"./Delete-892b5752.js";import q from"./DeleteBulk-75224491.js";import{_ as H}from"./Checkbox-70384e30.js";import K from"./Permission-3641a4e8.js";import"./index-73ed916b.js";import"./SecondaryButton-ee37c190.js";const Q={class:"space-y-4"},W={class:"px-4 sm:px-0"},Y={class:"rounded-lg overflow-hidden w-fit"},Z={class:"relative bg-white dark:bg-gray-800 shadow sm:rounded-lg"},ee={class:"flex justify-between p-2"},se={class:"flex space-x-2"},te={class:"overflow-x-auto scrollbar-table"},le={class:"w-full"},ae={class:"uppercase text-sm border-t border-gray-200 dark:border-gray-700"},re={class:"dark:bg-gray-900/50 text-left"},oe={class:"px-2 py-4 text-center"},ne=s("th",{class:"px-2 py-4 text-center"},"#",-1),ie={class:"flex justify-between items-center"},de={class:"flex justify-between items-center"},pe=s("span",null,"Guard",-1),ce={class:"px-2 py-4"},me={class:"flex justify-between items-center"},ue={class:"flex justify-between items-center"},fe=s("th",{class:"px-2 py-4 sr-only"},"Action",-1),he={class:"whitespace-nowrap py-4 px-2 sm:py-3 text-center"},ye=["value"],_e={class:"whitespace-nowrap py-4 px-2 sm:py-3 text-center"},be={class:"whitespace-nowrap py-4 px-2 sm:py-3"},ge={class:"whitespace-nowrap py-4 px-2 sm:py-3"},we=["onClick"],ke=["onClick"],ve={key:2,class:"whitespace-nowrap py-4 px-2 sm:py-3"},xe={class:"whitespace-nowrap py-4 px-2 sm:py-3"},$e={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Oe={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Ce={class:"flex justify-center items-center"},Se={class:"rounded-md overflow-hidden"},je={class:"flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700"},Re={__name:"Index",props:{title:String,filters:Object,roles:Object,permissions:Object,breadcrumbs:Object,perPage:Number},setup(h){const o=h,e=j({params:{search:o.filters.search,field:o.filters.field,order:o.filters.order,perPage:o.perPage},selectedId:[],multipleSelect:!1,createOpen:!1,editOpen:!1,deleteOpen:!1,deleteBulkOpen:!1,permissionOpen:!1,role:null,dataSet:I().props.app.perpage}),y=a=>{e.params.field=a,e.params.order=e.params.order==="asc"?"desc":"asc"};V(()=>g.exports._.cloneDeep(e.params),g.exports.debounce(()=>{let a=g.exports.pickBy(e.params);B.get(route("role.index"),a,{replace:!0,preserveState:!0,preserveScroll:!0})},150));const C=a=>{var t;a.target.checked===!1?e.selectedId=[]:(t=o.roles)==null||t.data.forEach(p=>{e.selectedId.push(p.id)})},S=()=>{var a;((a=o.roles)==null?void 0:a.data.length)==e.selectedId.length?e.multipleSelect=!0:e.multipleSelect=!1};return(a,t)=>{const p=N("tooltip");return i(),m(x,null,[r(d(D),{title:o.title},null,8,["title"]),r(E,null,{default:f(()=>[r(A,{title:h.title,breadcrumbs:h.breadcrumbs},null,8,["title","breadcrumbs"]),s("div",Q,[s("div",W,[s("div",Y,[c(r(J,{class:"rounded-none",onClick:t[0]||(t[0]=l=>e.createOpen=!0)},{default:f(()=>[w(n(a.lang().button.add),1)]),_:1},512),[[_,a.can(["create role"])]]),r(R,{show:e.createOpen,onClose:t[1]||(t[1]=l=>e.createOpen=!1),permissions:o.permissions,title:o.title},null,8,["show","permissions","title"]),r(T,{show:e.editOpen,onClose:t[2]||(t[2]=l=>e.editOpen=!1),role:e.role,permissions:o.permissions,title:o.title},null,8,["show","role","permissions","title"]),r(X,{show:e.deleteOpen,onClose:t[3]||(t[3]=l=>e.deleteOpen=!1),role:e.role,title:o.title},null,8,["show","role","title"]),r(q,{show:e.deleteBulkOpen,onClose:t[4]||(t[4]=l=>(e.deleteBulkOpen=!1,e.multipleSelect=!1,e.selectedId=[])),selectedId:e.selectedId,title:o.title},null,8,["show","selectedId","title"]),r(K,{show:e.permissionOpen,onClose:t[5]||(t[5]=l=>e.permissionOpen=!1),role:e.role,title:o.title},null,8,["show","role","title"])])]),s("div",Z,[s("div",ee,[s("div",se,[r(M,{modelValue:e.params.perPage,"onUpdate:modelValue":t[6]||(t[6]=l=>e.params.perPage=l),dataSet:e.dataSet},null,8,["modelValue","dataSet"]),c((i(),k(O,{onClick:t[7]||(t[7]=l=>e.deleteBulkOpen=!0),class:"px-3 py-1.5"},{default:f(()=>[r(d($),{class:"w-5 h-5"})]),_:1})),[[_,e.selectedId.length!=0&&a.can(["delete role"])],[p,a.lang().tooltip.delete_selected]])]),r(F,{modelValue:e.params.search,"onUpdate:modelValue":t[8]||(t[8]=l=>e.params.search=l),type:"text",class:"block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg",placeholder:a.lang().placeholder.search},null,8,["modelValue","placeholder"])]),s("div",te,[s("table",le,[s("thead",ae,[s("tr",re,[s("th",oe,[r(H,{checked:e.multipleSelect,"onUpdate:checked":t[9]||(t[9]=l=>e.multipleSelect=l),onChange:C},null,8,["checked"])]),ne,s("th",{class:"px-2 py-4 cursor-pointer",onClick:t[10]||(t[10]=l=>y("name"))},[s("div",ie,[s("span",null,n(a.lang().label.name),1),r(d(b),{class:"w-4 h-4"})])]),s("th",{class:"px-2 py-4 cursor-pointer",onClick:t[11]||(t[11]=l=>y("email"))},[s("div",de,[pe,r(d(b),{class:"w-4 h-4"})])]),s("th",ce,n(a.lang().label.permission),1),s("th",{class:"px-2 py-4 cursor-pointer",onClick:t[12]||(t[12]=l=>y("created_at"))},[s("div",me,[s("span",null,n(a.lang().label.created),1),r(d(b),{class:"w-4 h-4"})])]),s("th",{class:"px-2 py-4 cursor-pointer",onClick:t[13]||(t[13]=l=>y("updated_at"))},[s("div",ue,[s("span",null,n(a.lang().label.updated),1),r(d(b),{class:"w-4 h-4"})])]),fe])]),s("tbody",null,[(i(!0),m(x,null,P(h.roles.data,(l,v)=>(i(),m("tr",{key:v,class:"border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20"},[s("td",he,[c(s("input",{class:"rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary",type:"checkbox",onChange:S,value:l.id,"onUpdate:modelValue":t[14]||(t[14]=u=>e.selectedId=u)},null,40,ye),[[U,e.selectedId]])]),s("td",_e,n(++v),1),s("td",be,n(l.name),1),s("td",ge,n(l.guard_name),1),l.permissions.length==o.permissions.length?c((i(),m("td",{key:0,onClick:u=>(e.permissionOpen=!0,e.role=l),class:"whitespace-nowrap py-4 px-2 sm:py-3 cursor-pointer text-blue-600 dark:text-blue-400 font-bold underline"},[w(n(a.lang().label.all_permission),1)],8,we)),[[p,a.lang().tooltip.detail]]):l.permissions.length!=0?c((i(),m("td",{key:1,onClick:u=>(e.permissionOpen=!0,e.role=l),class:"whitespace-nowrap py-4 px-2 sm:py-3 cursor-pointer text-blue-600 dark:text-blue-400 font-bold underline"},[w(n(l.permissions.length)+" "+n(a.lang().label.permission),1)],8,ke)),[[p,a.lang().tooltip.detail]]):(i(),m("td",ve,n(a.lang().label.no_permission),1)),s("td",xe,n(l.created_at),1),s("td",$e,n(l.updated_at),1),s("td",Oe,[s("div",Ce,[s("div",Se,[c((i(),k(G,{type:"button",onClick:u=>(e.editOpen=!0,e.role=l),class:"px-2 py-1.5 rounded-none"},{default:f(()=>[r(d(L),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[_,a.can(["update role"])],[p,a.lang().tooltip.edit]]),c((i(),k(O,{type:"button",onClick:u=>(e.deleteOpen=!0,e.role=l),class:"px-2 py-1.5 rounded-none"},{default:f(()=>[r(d($),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[_,a.can(["delete role"])],[p,a.lang().tooltip.delete]])])])])]))),128))])])]),s("div",je,[r(z,{links:o.roles,filters:e.params},null,8,["links","filters"])])])])]),_:1})],64)}}};export{Re as default};