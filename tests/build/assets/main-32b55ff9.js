import{t as m}from"./shared-d0876a44.js";const h="modulepreload",d=function(o){return"/"+o},a={},g=function(i,l,u){if(!l||l.length===0)return i();const c=document.getElementsByTagName("link");return Promise.all(l.map(e=>{if(e=d(e),e in a)return;a[e]=!0;const n=e.endsWith(".css"),f=n?'[rel="stylesheet"]':"";if(!!u)for(let r=c.length-1;r>=0;r--){const s=c[r];if(s.href===e&&(!n||s.rel==="stylesheet"))return}else if(document.querySelector(`link[href="${e}"]${f}`))return;const t=document.createElement("link");if(t.rel=n?"stylesheet":h,n||(t.as="script",t.crossOrigin=""),t.href=e,document.head.appendChild(t),n)return new Promise((r,s)=>{t.addEventListener("load",r),t.addEventListener("error",()=>s(new Error(`Unable to preload CSS for ${e}`)))})})).then(()=>i())};console.log("ThemePlate!");console.log(`Shared: ${m}`);g(()=>import("./foo-d9b40e50.js"),[]).then(o=>console.log(`Foo ${o.default}`));
