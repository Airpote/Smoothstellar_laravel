/*!
 * Toastr - ICOCrypto v1.6.0 by Softnio.
**/
NioApp=function(t,o,e){"use strict";var s=o(".toastr-top-center"),n=o(".toastr-top-right"),i=o(".toastr-top-left"),a=o(".toastr-top-full"),r=o(".toastr-bottom-center"),c=o(".toastr-bottom-right"),u=o(".toastr-bottom-left"),l=o(".toastr-bottom-full"),p=o(".toastr-info"),f=o(".toastr-success"),h=o(".toastr-warning"),m=o(".toastr-error");return t.Toastr={},t.Toastr.ToastrJs=function(){s.exists()&&s.each(function(){o(this).on("click",function(t){toastr.clear(),toastr.options={closeButton:!0,newestOnTop:!1,preventDuplicates:!0,positionClass:"toast-top-center",showDuration:"1000",hideDuration:"10000",timeOut:"2000",extendedTimeOut:"1000"},toastr.info("This is a note for Info message on Top Center"),t.preventDefault()})}),n.exists()&&n.each(function(){o(this).on("click",function(t){toastr.clear(),toastr.options={closeButton:!0,newestOnTop:!1,preventDuplicates:!0,positionClass:"toast-top-right",showDuration:"1000",hideDuration:"10000",timeOut:"2000",extendedTimeOut:"1000"},toastr.info("This is a note for Info message on Top Right"),t.preventDefault()})}),i.exists()&&i.each(function(){o(this).on("click",function(t){toastr.clear(),toastr.options={closeButton:!0,newestOnTop:!1,preventDuplicates:!0,positionClass:"toast-top-left",showDuration:"1000",hideDuration:"10000",timeOut:"2000",extendedTimeOut:"1000"},toastr.info("This is a note for Info message on Top Left"),t.preventDefault()})}),a.exists()&&a.each(function(){o(this).on("click",function(t){toastr.clear(),toastr.options={closeButton:!0,newestOnTop:!1,preventDuplicates:!0,positionClass:"toast-top-full-width",showDuration:"1000",hideDuration:"10000",timeOut:"2000",extendedTimeOut:"1000"},toastr.info("This is a note for Info message on Top Full"),t.preventDefault()})}),r.exists()&&r.each(function(){o(this).on("click",function(t){toastr.clear(),toastr.options={closeButton:!0,newestOnTop:!1,preventDuplicates:!0,positionClass:"toast-bottom-center",showDuration:"1000",hideDuration:"10000",timeOut:"2000",extendedTimeOut:"1000"},toastr.info("This is a note for Info message on Bottom Center"),t.preventDefault()})}),c.exists()&&c.each(function(){o(this).on("click",function(t){toastr.clear(),toastr.options={closeButton:!0,newestOnTop:!1,preventDuplicates:!0,positionClass:"toast-bottom-right",showDuration:"1000",hideDuration:"10000",timeOut:"2000",extendedTimeOut:"1000"},toastr.info("This is a note for Info message on Bottom Right"),t.preventDefault()})}),u.exists()&&u.each(function(){o(this).on("click",function(t){toastr.clear(),toastr.options={closeButton:!0,newestOnTop:!1,preventDuplicates:!0,positionClass:"toast-bottom-left",showDuration:"1000",hideDuration:"10000",timeOut:"2000",extendedTimeOut:"1000"},toastr.info("This is a note for Info message on Bottom Left"),t.preventDefault()})}),l.exists()&&l.each(function(){o(this).on("click",function(t){toastr.clear(),toastr.options={closeButton:!0,newestOnTop:!1,preventDuplicates:!0,positionClass:"toast-bottom-full-width",showDuration:"1000",hideDuration:"10000",timeOut:"2000",extendedTimeOut:"1000"},toastr.info("This is a note for Info message on Bottom Full"),t.preventDefault()})}),p.exists()&&p.each(function(){o(this).on("click",function(t){toastr.clear(),toastr.options={closeButton:!0,newestOnTop:!1,preventDuplicates:!0,positionClass:"toast-bottom-center",showDuration:"1000",hideDuration:"10000",timeOut:"2000",extendedTimeOut:"1000"},toastr.info('<em class="ti ti-filter toast-message-icon"></em> This is a note for Info message'),t.preventDefault()})}),f.exists()&&f.each(function(){o(this).on("click",function(t){toastr.clear(),toastr.options={closeButton:!0,newestOnTop:!1,preventDuplicates:!0,positionClass:"toast-bottom-center",showDuration:"1000",hideDuration:"10000",timeOut:"2000",extendedTimeOut:"1000"},toastr.success('<em class="ti ti-check toast-message-icon"></em> This is a note for Success message'),t.preventDefault()})}),h.exists()&&h.each(function(){o(this).on("click",function(t){toastr.clear(),toastr.options={closeButton:!0,newestOnTop:!1,preventDuplicates:!0,positionClass:"toast-bottom-center",showDuration:"1000",hideDuration:"10000",timeOut:"2000",extendedTimeOut:"1000"},toastr.warning('<em class="ti ti-alert toast-message-icon"></em> This is a note for Warning message'),t.preventDefault()})}),m.exists()&&m.each(function(){o(this).on("click",function(t){toastr.clear(),toastr.options={closeButton:!0,newestOnTop:!1,preventDuplicates:!0,positionClass:"toast-bottom-center",showDuration:"1000",hideDuration:"10000",timeOut:"2000",extendedTimeOut:"1000"},toastr.error('<em class="ti ti-na toast-message-icon"></em> This is a note for Error message'),t.preventDefault()})})},t.components.docReady.push(t.Toastr.ToastrJs),t}(NioApp,jQuery,window);