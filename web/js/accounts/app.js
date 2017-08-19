let accountForm = {
  template: `
  <section>
    <div v-if="accountListLength > 0">
      <div class="field">
        <div class="control">
          <table class="table">
            <thead>
              <tr>
                <th>Cuenta</th>
                <th>Fecha</th>
                <th>Status</th>
                <th>Cerrar</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="account in accountList" :key="account.id">
                <td>{{ account.id }}</td>
                <td>{{ moment(account.checkin).format('MMMM D, h:mm:ss a') }}</td>
                <td v-if=" account.status">
                  <span class="tag is-success">Abierta</span>
                </td>
                <td v-else>
                  <span  class="tag is-danger">Cerrada</span>
                </td>
                <td v-if="account.status">
                  <a class="link" @click.prevent="closeAccount(account)">
                    <span class="icon">
                      <i class="fa fa-check-circle-o"></i>
                    </span>
                  </a>
                </td>
                <td v-else>
                  <a class="link" @click.prevent="printAccount(account)">
                    <span class="icon">
                      <i class="fa fa-print"></i>
                    </span>
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="control">
        <button class="button is-info" @click.prevent="printAllAccounts" style="margin-bottom: 10px;">
          <span class="icon is-normal">
            <i class="fa fa-print"></i>
          </span>
          <span>Imprimir todas las cuentas</span>
        </button>
      </div>      
    </div>
  </section>`,
  methods: {
    fetchAccounts () {
        axios.defaults.headers.common = {
            'X-Requested-With': 'XMLHttpRequest',
        };

        axios.get('/api/accounts/date')
            .then(response => {
                this.$store.commit('updateAccountList', response.data.accounts);
            })
            .catch(function (error) {
                console.log(error);
            });
    },
    closeAccount(account) {
        axios.defaults.headers.common = {
            'X-Requested-With': 'XMLHttpRequest',
        };

        axios.put('/api/accounts/close', account)
             .then(function (response) {
                if (response.data === 'success') {
                    swal('¡Correcto!', 'Cuenta cerrada satisfactoriamente', 'success');
                }
             })
             .catch(function (error) {
                console.log(error);
                swal('Error', 'Esta cuenta no pudo ser cerrada en el sistema', 'error')
             });
        this.fetchAccounts();
    },
    printAccount(account) {
        axios.get('/api/accounts/' + account.id)
             .then(function (response) {
                swal('¡Correcto!', 'Cuenta impresa satisfactoriamente', 'success');
             })
             .catch(function (error) {
                console.log(error);
             });
    },
    printAllAccounts() {
        axios.get('/api/accounts/all')
             .then(function (response) {
                swal('¡Correcto!', 'Cuentas impresas satisfactoriamente', 'success');
             })
             .catch(function (error) {
                console.log(error);
             });
    }
  },
  computed: {
    accountDate: {
        get () {
            return this.$store.state.accountDate
        },
        set (value) {
            this.$store.commit('updateAccountDate', value)
        }
    },
    accountList: {
      get () {
        return this.$store.state.accountList
      },
      set (value) {
        this.$store.commit('updateAccountList', value)
      }
    },
    accountListLength() {
      return this.$store.state.accountList.length;
    }
  }
};


Vue.use('vuex');

const store = new Vuex.Store({
  state: {
    accountDate: '',
    accountList: []
  },
  mutations: {
    updateAccountDate(state, accountDate) {
      state.accountDate = accountDate;
    },
    updateAccountList(state, accountList) {
      state.accountList = accountList;
    }
  }
});

new Vue({
    delimiters: ['${', '}'],
    el: 'main',
    store,
    components: { accountForm },
    mounted() {
        this.fetchAccounts();
    },
    methods: {
      fetchAccounts () {
          axios.get('/api/accounts/date')
              .then(response => {
                  this.$store.commit('updateAccountList', response.data.accounts);
              })
              .catch(function (error) {
                  console.log(error);
              });
      },
      createAccount () {
        axios.defaults.headers.common = {
          'X-Requested-With': 'XMLHttpRequest',
        };

        axios.post('/api/accounts/create')
           .then(function (response) {
             if(response.data === 'success') {
               swal('¡Correcto!', 'Cuenta registrada satisfactoriamente', 'success');

             }
           })
           .catch(function (error) {
             console.log(error);
             swal('Error', 'Esta cuenta no pudo ser registrada en el sistema', 'error')
           });
        // Clean form
        this.fetchAccounts();
      }
    }
});