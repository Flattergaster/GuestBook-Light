function tag(c,a){if(document.selection){document.getElementById("mess").focus();var b=document.post.document.selection.createRange().text;document.post.document.selection.createRange().text=c+b+a}else if(void 0!=document.forms.post.elements.msg.selectionStart){var b=document.forms.post.elements.msg,d=b.value,e=b.selectionStart,f=b.selectionEnd-b.selectionStart;b.value=d.substr(0,e)+c+d.substr(e,f)+a+d.substr(e+f)}return!1}