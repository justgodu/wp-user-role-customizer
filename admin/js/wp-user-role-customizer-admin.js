jQuery(function ($) {

    $(window).load(function() {
        $(':input','#add_role')
            .not(':button, :submit, :reset, :hidden')
            .prop('checked', false)
            .prop('selected', false);
    });
})

function clearInp() {
    document.getElementsByTagName("input").value = "";
}


function handleChange(checkbox, value) {
    var hiddens = document.getElementById("hiddeninput");
    if (checkbox.checked == true) {
        console.log(checkbox.checked);

        hiddens.innerHTML +=
            '<input name="permission[]" type="hidden" value = "' + value + '"/>';
    } else {
        var children = hiddens.children;
        var children_array = Object.entries(children);

        for (var i = 0; i < children_array.length; i++) {
            console.log(children_array[i][1].value, value);
            if (children_array[i][1].value == value) {
                children_array.splice(i, 1);
                break;
            }
        }

        hiddens.innerHTML = "";
        children_array.forEach((element) => {
            hiddens.appendChild(element[1]);
        });
    }
}