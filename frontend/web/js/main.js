$(function(){

    if( $(".feedback-form-box textarea").length ){
        $(".feedback-form-box textarea").textareaAutoSize();
    }


    if( $(".news-list-box").length ){
        $(".news-list-box").bxSlider({
            mode: 'vertical',
            infiniteLoop: false,
            hideControlOnEnd: true,
            pager: false
        });
    }

    if( $("select.choice").length ) {
        $("select.choice").select2({
            //allowClear: true,
            width: "resolve",
            minimumResultsForSearch: 15
        }).on("change", function(){
            var selectItem = $(this),
                idVal = "select2-" + selectItem.attr("id") + "-container",
                inputSpan = null,
                pseudoSpan = null,
                pseudoBtn = null;

            inputSpan = $("#" + idVal).parents(".select2-container").eq(0);
            if( selectItem.val() == ''){
                inputSpan.removeClass('not-empty');
            }
            else
            {
                inputSpan.addClass('not-empty');
                pseudoSpan = $("<span />")
                pseudoBtn = $("<span />")
                pseudoSpan.addClass("pseudo-span");
                pseudoBtn.attr("title", "Сбросить фильтр").addClass("pseudo-btn");

                pseudoBtn.on("click", function(){
                    selectItem.val('').change();
                    inputSpan.removeClass('not-empty');
                    pseudoSpan.remove();
                    $(this).remove();
                });

                inputSpan.append(pseudoSpan, pseudoBtn);
            }
        });
    }

    if( $(".input-file-box").length ) {
        $(".input-file-box .file-field").click(function(){
            fileDialogOpen(this);
        });

        $(".input-file-box .delete-item").click(function(){
            //removeFileInput(this);
        });

        $(".input-file-box .attach-item").click(function(){
            //addFileInput(this);

            var item = $(this).parent().find(".file-field");
            if(item.length){
                fileDialogOpen(item);
            }
        });
    }

    $('.phone-input').keypress(function(key) {
        if((key.charCode < 48 || key.charCode > 57) && key.charCode != 32 && key.charCode != 40 && key.charCode != 41 && key.charCode != 43 && key.charCode != 45) {
            return false;
        }
        else
        {

        }
        //alert(key.charCode);
        return true;
    });
});

function removeFileInput(target) {
    $(target).parents(".input-file-item").eq(0).remove();
}

function addFileInput(target){
    var spanAttachItem = $("<span />"),
        spanDeleteItem = $("<span />"),
        inputTxt = $("<input>");

    spanDeleteItem.addClass("img-btn delete-item").attr("title", "Удалить").click(function(){ removeFileInput(this); });
    $(target).parents(".input-file-item").eq(0).append(spanDeleteItem);

    spanAttachItem.addClass("img-btn attach-item").attr("title", "Прикрепить еще");
    spanAttachItem.click(function(){ addFileInput(this) });

    inputTxt.addClass("file-field").attr("type", "text").click(function(){ fileDialogOpen(this); });

    $(target).parents(".input-file-box").eq(0).append(
        $("<div />").addClass("input-file-item").append(
            inputTxt,
            spanAttachItem,
            $("<input>").addClass("file-field").attr({"type": "file", "name": "attached_file[]"})
        )
    );

    $(target).remove();
}

function addRemoveBtn(target){
    var spanDeleteItem = $("<span />"),
        attachItem = $(target).parent().find(".attach-item");

    spanDeleteItem.addClass("img-btn delete-item").attr("title", "Удалить").click(function(){
        $(this).css("display", "none");
        $(this).parent().find(".attach-item").css("display", "block");
        $(this).parent().find("input[type='file']").val("");
        $(this).parent().find(".file-field").val("");
    });

    $(target).parents(".input-file-item").eq(0).append(spanDeleteItem);
}

function fileDialogOpen(target){
    var inputTxt = $(target),
        file = inputTxt.parent().find("input[type='file']");

    file.click();
    file.change(function(){
        if(file.val()) {
            var deleteItem = inputTxt.parent().find(".delete-item");
            inputTxt.val(file.val());
            inputTxt.parent().find(".attach-item").css("display", "none");
            if (deleteItem.length) {
                deleteItem.css("display", "block");
            }
            else {
                addRemoveBtn(inputTxt);
            }
        }
    });
}