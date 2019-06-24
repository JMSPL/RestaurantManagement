<template>
    <div>
        <vuetable
                ref="vuetable"
                :api-mode="false"
                :data="items"
                :fields="fields"
                :css="css.table"
        >
            <div slot="type" slot-scope="props">
                <i class="material-icons tooltipped" data-position="right" data-tooltip="Dish" v-if="props.rowData.type === 'dish'">local_dining</i>
                <i class="material-icons tooltipped" data-position="right" data-tooltip="Drink" v-else>local_bar</i>
            </div>
        </vuetable>
    </div>
</template>

<script>
    import Vuetable from 'vuetable-2';
    const myScripts = require('../../../../public/js/scripts');

    export default {
        name: "invoice-items",
        props:['invoice'],
        components: {Vuetable},
        data(){
            return {
                items: [],
                fields:[
                    {
                        name: 'name',
                        title:'Item',
                    },
                    {
                        name: 'type',
                        title:'Type',
                    },
                    {
                        name: 'quantity',
                        title:'Quantity',
                    },
                    {
                        name: 'unit_price',
                        title:'Unit Price',
                        callback: "price",
                    },
                    {
                        name: 'sub_total_price',
                        title:'Sub total price',
                        callback: "price",
                    },
                ],
                css: {
                    table: {
                        tableWrapper: "",
                        tableHeaderClass: "mb-0",
                        tableBodyClass: "mb-0",
                        tableClass: "table table-bordered table-hover",
                        loadingClass: "loading",
                        ascendingIcon: "arrow_upward",
                        descendingIcon: "arrow_downward",
                        ascendingClass: "sorted-asc",
                        descendingClass: "sorted-desc",
                        sortableIcon: "swap_vert",
                        detailRowClass: "vuetable-detail-row",
                        handleIcon: "fa-bars text-secondary",
                        renderIcon(classes, options) {
                            return `<i class="material-icons" ${options}>${classes[1]}</span>`;
                        },
                    },
                    pagination: {
                        wrapperClass: 'pagination',
                        activeClass: 'active',
                        disabledClass: 'disabled',
                        pageClass: 'page',
                        linkClass: 'link',
                        icons: {
                            first: '',
                            prev: '',
                            next: '',
                            last: '',
                        },
                    },
                }
            }
        },
        methods:{
            stateColor(state){
                switch (state) {
                    case "pending":
                        return "orange";
                    case 'confirmed':
                        return "light-green";
                    case 'in preparation':
                        return "amber darken-1";
                    case 'prepared':
                        return "blue";
                    case 'delivered':
                        return "green";
                    case 'not delivered':
                        return "red";
                }
            },
            price(value){
                return value+" €";
            },
        },
        watch:{
            meal(){
                // O TOOLTIP SO FUNCIONA COM ISTO MESMO A 0(ZERO)
                setTimeout(function() {
                    var elems = document.querySelectorAll('.tooltipped');
                    M.Tooltip.init(elems);
                }, 0);
            }
        },
        mounted() {
            let request = axios.get('/api/invoices/' + this.invoice.id + '/items');
            request.then(res => this.items = res.data.data);
        }
    }
</script>

<style scoped>

</style>
