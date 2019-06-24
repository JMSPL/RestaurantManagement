<template>
    <div class="row">
        <div class="col s12">
            <h4>Waiters</h4>
            <div class="progress" v-if="!waitersLoaded">
                <div class="indeterminate"></div>
            </div>
            <bar-chart
                    v-if="waitersLoaded"
                    :chartdata="waiters"
                    :options="options"
                    :height="200"/>
        </div>
        <div class="col s12">
            <h4>Cooks</h4>
            <div class="progress" v-if="!cooksLoaded">
                <div class="indeterminate"></div>
            </div>
            <bar-chart
                    v-if="cooksLoaded"
                    :chartdata="cooks"
                    :options="options"
                    :height="200"/>
        </div>
    </div>
</template>

<script>
    import BarChart from './BarChart';

    export default {
        name: "statistics",
        components: {
            BarChart
        },
        data(){
            return {
                waitersLoaded: false,
                cooksLoaded: false,
                waiters: null,
                options: null,
                cooks: null,
            }
        },
        created(){
            let request = axios.get('/api/statistics/waiters');
            request.then(res => {
                this.waiters = {};
                this.waiters.labels = [];
                let waitersMealsPerDay = []
                let waitersOrdersPerDay = []
                for(let i in res.data){
                    this.waiters.labels.push(res.data[i].waiter);
                    waitersMealsPerDay.push(res.data[i].meals_per_day)
                    waitersOrdersPerDay.push(res.data[i].orders_per_day)
                }
                this.waiters.datasets = [
                    {
                        label: 'Meals per day',
                        backgroundColor: '#800000',
                        data: waitersMealsPerDay,
                    },
                    {
                        label: 'Orders per day',
                        backgroundColor: '#2196F3',
                        data: waitersOrdersPerDay,
                    }
                ];
                this.waitersLoaded = true;
            });
            request.catch(res => M.toast({html:'Error!', classes:'red'}));

            request = axios.get('/api/statistics/cooks');
            request.then(res => {
                this.cooks = {};
                this.cooks.labels = [];
                let cooksOrdersPerDay = []
                for(let i in res.data){
                    this.cooks.labels.push(res.data[i].cook);
                    cooksOrdersPerDay.push(res.data[i].orders_per_day)
                }
                this.cooks.datasets = [
                    {
                        label: 'Orders per day',
                        backgroundColor: '#2196F3',
                        data: cooksOrdersPerDay,
                    }
                ];
                this.cooksLoaded = true;
            });
            request.catch(res => M.toast({html:'Error!', classes:'red'}));
        }
    }
</script>

<style scoped>

</style>