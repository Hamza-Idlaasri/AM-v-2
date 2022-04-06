const hosts = document.getElementById('hosts-btn');
const services = document.getElementById('services-btn');
const boxes = document.getElementById('boxes-btn');
const equips = document.getElementById('equips-btn');

const hosts_table = document.getElementById('hosts-notifs');
const services_table = document.getElementById('services-notifs');
const boxes_table = document.getElementById('boxes-notifs');
const equips_table = document.getElementById('equips-notifs');

const btn_list = document.getElementById('btn-list');

btn_list.addEventListener('click', (event) => {
    const element = document.getElementById(event.target.id);
    steps(element);
})


function steps(element) {

    switchBtnType(element);

    removeBtnPrimary(element);

    displayElement(element);

    element.firstElementChild.style.display = 'none';

}

function switchBtnType(element) {

    switch (element) {

        case hosts:

            hosts.classList.remove("btn-light");
            hosts.classList.add("btn-primary");

            break;

        case services:

            services.classList.remove("btn-light");
            services.classList.add("btn-primary");

            break;

        case boxes:

            boxes.classList.remove("btn-light");
            boxes.classList.add("btn-primary");

            break;

        case equips:

            equips.classList.remove("btn-light");
            equips.classList.add("btn-primary");

            break;

    }
}

function removeBtnPrimary(element) {

    switch (element) {

        case hosts:

            boxes.classList.remove("btn-primary");
            services.classList.remove("btn-primary");
            equips.classList.remove("btn-primary");

            break;

        case services:

            hosts.classList.remove("btn-primary");
            boxes.classList.remove("btn-primary");
            equips.classList.remove("btn-primary");

            break;

        case boxes:

            hosts.classList.remove("btn-primary");
            services.classList.remove("btn-primary");
            equips.classList.remove("btn-primary");

            break;

        case equips:

            hosts.classList.remove("btn-primary");
            services.classList.remove("btn-primary");
            boxes.classList.remove("btn-primary");

            break;

    }
}

function displayElement(element) {

    switch (element) {

        case hosts:

            hosts_table.style.display = 'block';
            services_table.style.display = 'none';
            boxes_table.style.display = 'none';
            equips_table.style.display = 'none';

            break;

        case services:

            services_table.style.display = 'block';
            hosts_table.style.display = 'none';
            boxes_table.style.display = 'none';
            equips_table.style.display = 'none';

            break;

        case boxes:

            boxes_table.style.display = 'block';
            hosts_table.style.display = 'none';
            services_table.style.display = 'none';
            equips_table.style.display = 'none';

            break;

        case equips:

            equips_table.style.display = 'block';
            hosts_table.style.display = 'none';
            boxes_table.style.display = 'none';
            services_table.style.display = 'none';

            break;

    }
}


document.getElementById('icon-bell').classList.remove('far');
document.getElementById('icon-bell').classList.add('fas');
document.getElementById('icon-bell').style.color = '#3490dc';