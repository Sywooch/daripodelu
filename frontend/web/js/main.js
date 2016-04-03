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

    if ($(".products-list .product-item").length) {
        $(".products-list .product-item").each(function(){
            $(this).attr("data-height", $(this).height());
        });

        $(".products-list .product-item").hover(
            function(){
                $(this).css({marginBottom: $(this).attr("data-height") + "px"});
            },
            function(){
                $(this).css({marginBottom: "0px"});
            }
        );
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

    var tpContainer = $(".shop-cart .cart-total-price");
    if (tpContainer.length)
    {
        tpContainer.text(separateOnTetradBySpace(tpContainer.text()));
    }
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

function calcSum(obj, price)
{
    var totalCount = 0,
        totalInfoObj = obj.find(".total-info"),
        totalPrice = 0;

    obj.find("input.size-count").each(function(){
        var val = parseInt($(this).val());

        if (isNaN(val))
        {
            val = 0;
        }
        totalCount = totalCount + val;
    });

    totalPrice = totalCount * price;

    totalInfoObj.find(".total-count").text(totalCount);
    totalInfoObj.find(".total-price").html(decoratePrice(totalPrice, "руб.", ","));

    if (totalCount == 0)
    {
        totalInfoObj.hide();
    }
    else
    {
        totalInfoObj.show();
    }
}

function decoratePrice(price, currency, delimiter)
{
    var priceStr = "",
        integerPart = "",
        fractionalPart = "",
        arr = String(price).split(/[,\.]/);

    if (arr.length == 1)
    {
        integerPart = arr[0];
    }
    else if (arr.length > 1)
    {
        integerPart = arr[0];
        fractionalPart = arr[1];
    }
    else
    {
        return price;
    }

    if (fractionalPart == "")
    {
        fractionalPart = "00";
    }

    priceStr = separateOnTetradBySpace(integerPart) + delimiter + '<span class="small">' + fractionalPart + " " + currency + "</span>"

    return priceStr;
}

function separateOnTetradBySpace(str)
{
    return String(str).replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g,"\$1 ");
}

function changeTotalPrice($totalPrice)
{
    var totalPriceContainer = $(".shop-cart .cart-total-price");

    if (totalPriceContainer.length)
    {
        $totalPrice = separateOnTetradBySpace($totalPrice);
        totalPriceContainer.text($totalPrice);
    }
}

function showModal(id, header, body)
{
    var modal = $('<div />'),
        modalContent = $('<div />'),
        modalHeader = $('<div />'),
        modalBody = $('<div />'),
        closeBtn = $('<button />'),
        overlay = $('<div />'),
        modalDialog = $('<div />'),
        top = '';

    modal.attr({
        id: id,
        role: "dialog",
        tabindex: "-1"
    }).addClass('fade modal');

    modalDialog.addClass('modal-dialog');
    modalDialog.css('opacity', 0);
    modalContent.addClass('modal-content');
    modalHeader.addClass('modal-header');
    modalBody.addClass('modal-body');
    modalBody.html(body);
    closeBtn.attr({
        type: "button",
        title: "Закрыть"
    }).addClass('close');
    closeBtn.text('×');
    overlay.addClass('modal-overlay');
    overlay.css('display', 'none');

    modalHeader.append(
        closeBtn,
        '<h2>' + header + '<h2>'
    )
    modalContent.append(modalHeader, modalBody);
    modalDialog.append(modalContent);
    modal.append(overlay, modalDialog);

    $('body').append(modal);
    top = modalDialog.offset().top;
    modalDialog.css('top', '-10px');

    closeBtn.on('click', function(){
        modal.fadeOut(200, 'swing', function(){
            modal.remove();
        });
    });
    overlay.on('click', function(){ closeBtn.click(); });

    overlay.fadeIn(200);
    modalDialog.animate({
        top: top + 'px',
        opacity: 1
    }, 400);
}