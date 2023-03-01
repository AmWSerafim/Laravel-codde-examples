$(".dropify").dropify({
    messages: {
        default: "Drag and drop a file here or click",
        replace: "Drag and drop or click to replace",
        remove: "Remove",
        error: "Ooops, something wrong appended."
    }, error: {fileSize: "The file size is too big (1M max)."}
});

jQuery(document).ready(function () {

    $(".touch-spin-custom").TouchSpin({
        initval: 1,
        buttondown_class: "btn btn-primary",
        buttonup_class: "btn btn-primary"
    });
});
