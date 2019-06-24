
module.exports = {
    startInstances: function () {
        var elems;
        /////// DROPDOWNN ///////
        elems = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(elems, {coverTrigger: false});

        /////// SELECT ///////
        elems = document.querySelectorAll('select#select');
        M.FormSelect.init(elems);

        /////// TOOLTIP ///////
        elems = document.querySelectorAll('.tooltipped');
        M.Tooltip.init(elems);

        /////// COLLAPSIBLE ///////
        elems = document.querySelectorAll('.collapsible');
         M.Collapsible.init(elems);

        /////// MODAL ///////
        elems = document.querySelectorAll('.modal');
        M.Modal.init(elems);

        /////// TABS ///////
        elems = document.querySelectorAll('.tabs');
        M.Tabs.init(elems);

        /////// DATE PICKER ///////
        elems = document.querySelectorAll('.datepicker');
        M.Datepicker.init(elems, {format:'yyyy-mm-dd'});

        /////// SIDENAV ///////
        elems = document.querySelectorAll('.sidenav');
        M.Sidenav.init(elems);

        /////// FlOATING BUTTON ///////
        elems = document.querySelectorAll('.fixed-action-btn');
        M.FloatingActionButton.init(elems);
    }
};