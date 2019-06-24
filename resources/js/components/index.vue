<template>
    <div>
        <div class="row">
            <div class="col s12 m6 l6" v-if="this.user !== null">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">
                            Shift Info
                        </span>
                        <div v-if="this.user.shift_active === 0">
                            <p>Last shift ended at: {{ this.user.last_shift_end }}</p>
                            <p>Time passed: {{ moment(this.user.last_shift_end).fromNow() }}</p>

                            <button style="margin-top:5px;" class="btn waves-effect" @click="startShift">Start Shift</button>
                        </div>
                        <div v-else>
                            <p>Shift started at: {{ this.user.last_shift_start }}</p>
                            <p>Time passed: {{ moment(this.user.last_shift_start).fromNow() }}</p>
                            <button style="margin-top:5px;" class="btn waves-effect" @click="endShift">End Shift</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const myScripts = require('../../../public/js/scripts');
    const moment = require('moment');
    export default {
        name: "index",
        data(){
            return{
                moment: moment,
                user: this.$store.state.user,
            }
        },
        methods:{
            startShift(){
                let request = axios.put('/api/workers/'+this.user.id+'/startshift');
                request.then(res => {
                    M.toast({html: 'You have started your shift!'});
                    this.$store.dispatch('updateUser', res.data);
                    this.$socket.emit('shift_start', res.data);
                    this.user = this.$root.user = res.data;
                });
            },
            endShift(){
                let request = axios.put('/api/workers/'+this.user.id+'/endshift');
                request.then(res => {
                    M.toast({html: 'You have ended your shift!'});
                    this.$store.dispatch('updateUser', res.data);
                    this.$socket.emit('shift_end', res.data);
                    this.user = this.$root.user = res.data;
                });
            },
        },
        mounted(){
            myScripts.startInstances();
            if(axios.defaults.headers.common.Authorization !== "Bearer null" && this.user === null){
                axios.get('/api/user')
                    .then(res => {
                        this.user = this.$root.user = res.data;
                    })
                    .catch(res => {
                        console.log(res);
                    });
            }
        },
        created(){

        }
    }
</script>

<style scoped>

</style>