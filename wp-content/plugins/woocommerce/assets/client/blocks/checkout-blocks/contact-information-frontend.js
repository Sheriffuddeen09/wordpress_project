"use strict";(self.webpackChunkwebpackWcBlocksCartCheckoutFrontendJsonp=self.webpackChunkwebpackWcBlocksCartCheckoutFrontendJsonp||[]).push([[3398],{9017:(e,t,o)=>{o.d(t,{A:()=>s});var c=o(7723);const s=({defaultTitle:e=(0,c.__)("Step","woocommerce"),defaultDescription:t=(0,c.__)("Step description text.","woocommerce"),defaultShowStepNumber:o=!0})=>({title:{type:"string",default:e},description:{type:"string",default:t},showStepNumber:{type:"boolean",default:o}})},8011:(e,t,o)=>{o.r(t),o.d(t,{default:()=>y});var c=o(1609),s=o(851),a=o(1616),r=o(4656),n=o(7143),l=o(7594),i=o(9292),u=o(7723),d=o(5251),m=o(3603),_=o(2379),p=o(5703),C=o(812),w=o(3505),h=o(6087),E=o(7926);const g=()=>{const[e,t]=(0,h.useState)(0),{customerPassword:o}=(0,n.useSelect)((e=>({customerPassword:e(l.CHECKOUT_STORE_KEY).getCustomerPassword()}))),{__internalSetCustomerPassword:s}=(0,n.useDispatch)(l.CHECKOUT_STORE_KEY);return(0,c.createElement)(r.ValidatedTextInput,{type:"password",label:(0,u.__)("Create a password","woocommerce"),className:"wc-block-components-address-form__password",value:o,required:!0,errorId:"account-password",customValidityMessage:e=>{if(e.valueMissing||e.badInput||e.typeMismatch)return(0,u.__)("Please enter a valid password","woocommerce")},customValidation:t=>!(e<2&&(t.setCustomValidity((0,u.__)("Please create a stronger password","woocommerce")),1)),onChange:e=>s(e),feedback:(0,c.createElement)(E.Ay,{password:o,onChange:e=>t(e)})})},S="wc-guest-checkout-notice",k=()=>{const{shouldCreateAccount:e}=(0,n.useSelect)((e=>({shouldCreateAccount:e(l.CHECKOUT_STORE_KEY).getShouldCreateAccount()}))),{__internalSetShouldCreateAccount:t,__internalSetCustomerPassword:o}=(0,n.useDispatch)(l.CHECKOUT_STORE_KEY),s=(0,p.getSetting)("checkoutAllowsGuest",!1),a=(0,p.getSetting)("checkoutAllowsSignup",!1),i=s&&a,d=!(0,p.getSetting)("generatePassword",!1)&&(i&&e||!s);return s||i||d?(0,c.createElement)(c.Fragment,null,s&&(0,c.createElement)("p",{id:S,className:"wc-block-checkout__guest-checkout-notice"},(0,u.__)("You are currently checking out as a guest.","woocommerce")),i&&(0,c.createElement)(r.CheckboxControl,{className:"wc-block-checkout__create-account",label:(0,u.sprintf)(/* translators: Store name */ /* translators: Store name */
(0,u.__)("Create an account with %s","woocommerce"),(0,p.getSetting)("siteTitle","")),checked:e,onChange:e=>{t(e),o("")}}),d&&(0,c.createElement)(g,null)):null},b=()=>{const{additionalFields:e,customerId:t}=(0,n.useSelect)((e=>{const t=e(l.CHECKOUT_STORE_KEY);return{additionalFields:t.getAdditionalFields(),customerId:t.getCustomerId()}})),{setAdditionalFields:o}=(0,n.useDispatch)(l.CHECKOUT_STORE_KEY),{billingAddress:s,setEmail:a}=(0,d.C)(),{dispatchCheckoutEvent:i}=(0,m.y)(),u={email:s.email,...e};return(0,c.createElement)(c.Fragment,null,(0,c.createElement)(r.StoreNoticesContainer,{context:_.tG.CONTACT_INFORMATION}),(0,c.createElement)(w.l,{id:"contact",addressType:"contact",ariaDescribedBy:S,onChange:e=>{const{email:t,...c}=e;a(t),i("set-email-address"),o(c)},values:u,fields:C.fO},!t&&(0,c.createElement)(k,null)))},f={...(0,o(9017).A)({defaultTitle:(0,u.__)("Contact information","woocommerce"),defaultDescription:(0,u.__)("We'll use this email to send you details and updates about your order.","woocommerce")}),className:{type:"string",default:""},lock:{type:"object",default:{remove:!0,move:!0}}},T=`${C.aW}?redirect_to=${encodeURIComponent(window.location.href)}`,O=()=>{const e=(0,n.useSelect)((e=>e(l.CHECKOUT_STORE_KEY).getCustomerId()));return!(0,p.getSetting)("checkoutShowLoginReminder",!0)||e?null:(0,c.createElement)("a",{className:"wc-block-checkout__login-prompt",href:T},(0,u.__)("Log in","woocommerce"))},y=(0,a.withFilteredAttributes)(f)((({title:e,description:t,children:o,className:a})=>{const u=(0,n.useSelect)((e=>e(l.CHECKOUT_STORE_KEY).isProcessing())),{showFormStepNumbers:d}=(0,i.Oy)();return(0,c.createElement)(r.FormStep,{id:"contact-fields",disabled:u,className:(0,s.A)("wc-block-checkout__contact-fields",a),title:e,description:t,showStepNumber:d,stepHeadingContent:()=>(0,c.createElement)(O,null)},(0,c.createElement)(b,null),o)}))}}]);