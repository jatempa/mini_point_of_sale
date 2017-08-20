let accountForm = {
  template: `
  <section>
    <div v-if="accountListLength > 0">
      <div class="modal is-active" v-if="showModal">
        <div class="modal-background"></div>      
        <div class="modal-content">
          <div class="box">
            <p>
              Lorem ipsum dolor sit amet, consectetur adipisicing elit. At autem error obcaecati. Aliquam debitis incidunt inventore quia temporibus, voluptas. Delectus distinctio doloribus ea fugiat maiores natus necessitatibus nisi soluta ullam!
            </p>
          </div>
        </div>
        <button class="modal-close is-large" aria-label="close" @click.prevent="closeModal"></button>      
      </div>
      <div class="field">
        <div class="control">
          <table class="table">
            <thead>
              <tr>
                <th>Cuenta</th>
                <th>Nombre</th>
                <th>Apertura</th>
                <th>Status</th>
                <th>Cerrar</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="account in accountList" :key="account.id">
                <td>
                  <a @click.prevent="showAccountDetails">
                    {{ account.id }}
                  </a>
                </td>
                <td>{{ account.name }}</td>
                <td>{{ moment(account.checkin).format('h:mm:ss a') }}</td>
                <td v-if=" account.status">
                  <span class="tag is-primary">Abierta</span>
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
    </div>
    <div v-else>
      <h4>Debes crear una cuenta para comenzar</h4>
    </div>
    <div class="field">
      <label class="label">Mesa</label>
      <div class="control">
        <input class="input" type="text" placeholder="Por ejemplo: Mesa de mi cliente favorito" v-model="accountName">
      </div>
    </div>   
  </section>`,
  data() {
    return {
      showModal: false
    }
  },
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
    showAccountDetails() {
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
    }
  },
  computed: {
    accountName: {
        get () {
            return this.$store.state.accountName
        },
        set (value) {
            this.$store.commit('updateAccountName', value)
        }
    },
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
    accountName: '',
    accountDate: '',
    accountList: []
  },
  mutations: {
    updateAccountName(state, accountName) {
      state.accountName = accountName;
    },
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

        let accountData = null;

        if(this.$store.state.accountName !== '') {
          accountData = {
            name: this.$store.state.accountName
          };
        }

        axios.post('/api/accounts/create', accountData)
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
        this.cleanForm();
        // Update list
        this.fetchAccounts();
      },
      cleanForm () {
        this.$store.commit('updateAccountName', '');
      }
    }
});