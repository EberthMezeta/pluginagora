const FORM = document.getElementsByClassName('mform')[0];
const HiddenList = document.getElementsByName('fileslist')[0];

console.log(FORM);

var ids = [];


FORM.addEventListener('submit', function(e) {
    e.preventDefault();


    if (ids.length == 0) {
        return false;
    }

    let idsString = ids.join(',');
    HiddenList.value = idsString;
    FORM.submit();

});

FORM.addEventListener('click', function(e) {
    if (e.target.type === 'checkbox') {
        const checkbox = e.target;
        let idfile = checkbox.getAttribute("fileid");
  
        index = ids.indexOf(idfile);
  
        if (index > -1) {
            ids.splice(index, 1);
        } else {
            ids.push(idfile);
        }
    }
});

