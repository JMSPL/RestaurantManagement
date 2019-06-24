<template>
    <div>
        <vuetable
                ref="vuetable"
                :api-mode="false"
                :data="meal.orders"
                :fields="fields"
                pagination-path=""
                :css="css.table"
        >
            <div slot="type" slot-scope="props">
                <i class="material-icons tooltipped" data-position="right" data-tooltip="Dish" v-if="props.rowData.item.type === 'dish'">local_dining</i>
                <i class="material-icons tooltipped" data-position="right" data-tooltip="Drink" v-else>local_bar</i>
            </div>
            <div slot="state" slot-scope="props">
                <span :class="'new badge '+stateColor(props.rowData.state)" data-badge-caption="">{{ props.rowData.state }}</span>
            </div>
        </vuetable>
    </div>
</template>

<script>
    import Vuetable from 'vuetable-2';
    const myScripts = require('../../../../public/js/scripts');

    export default {
        name: "meal-details",
        props:['meal'],
        components: {Vuetable},
        data(){
            return {
                fields:[
                    {
                        name: 'item.name',
                        title:'Item',
                    },
                    {
                        name: '__slot:type',
                        title:'Type',
                    },
                    {
                        name: 'item.price',
                        title:'Price',
                        callback: "price",
                    },
                    {
                        name: '__slot:state',
                        title:'State',
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
        }
    }
</script>

<style scoped>

</style>