"use strict";
!function(e, t) {
    e.SummerNote = function() {
        var e = ".summernote-basic"
          , e = (t(e).exists() && t(e).each(function() {
            t(this).summernote({
                placeholder: "Hello stand alone ui",
                tabsize: 2,
                height: 120,
                toolbar: [["style", ["style"]], ["font", ["bold", "underline", "strikethrough", "clear"]], ["font", ["superscript", "subscript"]], ["color", ["color"]], ["fontsize", ["fontsize", "height"]], ["para", ["ul", "ol", "paragraph"]], ["table", ["table"]], ["insert", ["link", "picture", "video"]], ["view", ["fullscreen", "codeview", "help"]]]
            })
        }),
        ".summernote-minimal");
        t(e).exists() && t(e).each(function() {
            t(this).summernote({
                placeholder: "Hello stand alone ui",
                tabsize: 2,
                height: 120,
                toolbar: [["style", ["style"]], ["font", ["bold", "underline", "clear"]], ["para", ["ul", "ol", "paragraph"]], ["table", ["table"]], ["view", ["fullscreen"]]]
            })
        })
    }
    ,
    e.Tinymce = function() {
        var e = ".tinymce-basic"
          , e = (t(e).exists() && tinymce.init({
            selector: e,
            content_css: !0,
            skin: !1,
            branding: !1
        }),
        ".tinymce-menubar")
          , e = (t(e).exists() && tinymce.init({
            selector: e,
            content_css: !0,
            skin: !1,
            branding: !1,
            toolbar: !1
        }),
        ".tinymce-toolbar")
          , e = (t(e).exists() && tinymce.init({
            selector: e,
            content_css: !0,
            skin: !1,
            branding: !1,
            menubar: !1
        }),
        ".tinymce-inline");
        t(e).exists() && tinymce.init({
            selector: e,
            content_css: !1,
            skin: !1,
            branding: !1,
            menubar: !1,
            inline: !0,
            toolbar: ["undo redo | bold italic underline | fontselect fontsizeselect", "forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent"]
        })
    }
    ,
    e.Quill = function() {
        var e = ".quill-basic"
          , e = (t(e).exists() && t(e).each(function() {
            new Quill(this,{
                modules: {
                    toolbar: [["bold", "italic", "underline", "strike"], ["blockquote", "code-block"], [{
                        list: "ordered"
                    }, {
                        list: "bullet"
                    }], [{
                        script: "sub"
                    }, {
                        script: "super"
                    }], [{
                        indent: "-1"
                    }, {
                        indent: "+1"
                    }], [{
                        header: [1, 2, 3, 4, 5, 6]
                    }], [{
                        color: []
                    }, {
                        background: []
                    }], [{
                        font: []
                    }], [{
                        align: []
                    }], ["clean"]]
                },
                theme: "snow"
            })
        }),
        ".quill-minimal");
        t(e).exists() && t(e).each(function() {
            new Quill(this,{
                modules: {
                    toolbar: [["bold", "italic", "underline"], ["blockquote", {
                        list: "bullet"
                    }], [{
                        header: 1
                    }, {
                        header: 2
                    }, {
                        header: [3, 4, 5, 6, !1]
                    }], [{
                        align: []
                    }], ["clean"]]
                },
                theme: "snow"
            })
        })
    }
    ,
    e.EditorInit = function() {
        e.SummerNote(),
        e.Tinymce(),
        e.Quill()
    }
    ,
    e.coms.docReady.push(e.EditorInit)
}(NioApp, jQuery);
