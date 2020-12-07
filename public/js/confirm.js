function confirmIt(id) {
    var result = confirm("Czy na pewno wygenrować wyniki?\n Zakończy to konkurs.");
    if (result) {
        window.location.href = "/contest/"+ id +"/gen_res";
    };
}