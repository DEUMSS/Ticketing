import 'jquery';
import "bootstrap"
import "bootstrap-table"


function sendFetch( paramList, options={}, responseType = 'json' ) {
    if (typeof options === "object") {
        options = {
            method: 'POST',
            body: JSON.stringify(paramList)
        }
    }
  //  console.log( options )
    return fetch(paramList.url, options)
        .then(response => {
            if (!response.ok) {
                throw new Error( response.url + ' ' + response.status + ' ' + response.statusText );
            }
            if (responseType === 'text') {
                return response.text();
            } else {
                return response.json();
            }
        })
}



$('#table').on( 'click', '.active-line', function(e){
    const elem = e.target

    const isActif = elem.dataset.isactif == 0 ? 1 : 0
    const url = elem.dataset.url + isActif

    console.log( isActif )
    elem.dataset.isactif = isActif

    const paramList = {
        url: url
    }

    sendFetch( paramList )
        .then( response => {
            return response
        })
        .then( data => {
            console.log( data )
        })
        .catch(error => {
            console.error( 'Erreur :', error )
        })

})

$('#table').on( 'click', '.admin-line', function(e){
    const elem = e.target

    const isAdmin = elem.dataset.isadmin == 0 ? 1 : 0
    const url = elem.dataset.url + isAdmin
    const adminSwitches = document.querySelectorAll('.admin-line');
    if (elem.checked){
        adminSwitches.forEach(function(switchElem){
            if (switchElem != elem){
                switchElem.checked = false;
                switchElem.dataset.isadmin = 0;
            }
        })
        elem.dataset.isadmin = 1;
    }
    
    elem.dataset.isadmin = isAdmin

    const paramList = {
        url: url
    }

    sendFetch( paramList )
        .then( response => {
            return response
        })
        .then( data => {
            console.log( data )
        })
        .catch(error => {
            console.error( 'Erreur :', error )
        })

})