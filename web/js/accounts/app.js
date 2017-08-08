let accountForm = {
  template: `
  <div>
      <div v-if="accountListLength > 0" class="field">
        <div class="control">
          <table class="table">
            <thead>
              <tr>
                <th>No.</th>
                <th>Mesa</th>
                <th>Apertura</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="account in accountList" :key="account.id">
                <td>{{ account.id }}</td>
                <td>{{ account.mesa }}</td>
                <td>{{ account.checkin }}</td>
                <td v-if=" account.status">
                  <span class="tag is-success">Abierta</span>
                </td>
                <td v-else>
                  <span  class="tag is-danger">Cerrada</span>
                </td>
                <td v-if="account.status">
                  <a class="link" @click.prevent="closeAccount(account)">
                    <span class="icon">
                      <i class="fa fa-remove"></i>
                    </span>
                  </a>
                </td>
                <td v-else>
                  <a class="link">
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
      <div class="field">
        <label class="label">Selecciona una mesa</label>
        <div class="control">
          <div class="select">
            <select v-model="selectedTable">
              <option v-for="mesa in mesasList" :value="mesa.id">
                {{ mesa.name }}
              </option>
            </select>
          </div>
        </div>
      </div>  
  </div>`,
  methods: {
    fetchAccounts () {
        axios.get('/api/accounts')
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
    }
  },
  computed: {
    selectedTable: {
        get () {
            return this.$store.state.selectedTable
        },
        set (value) {
            this.$store.commit('updateSelectedTable', value)
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
    mesasList: {
      get () {
        return this.$store.state.mesasList
      },
      set (value) {
        this.$store.commit('updateMesasList', value)
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
    selectedTable: 0,
    accountList: [],
    mesasList: []
  },
  mutations: {
    updateAccountList(state, accountList) {
      state.accountList = accountList;
    },
    updateMesasList(state, mesasList) {
      state.mesasList = mesasList;
    },
    updateSelectedTable(state, selectedTable) {
      state.selectedTable = selectedTable;
    }
  }
});

new Vue({
    delimiters: ['${', '}'],
    el: 'main',
    store,
    components: { accountForm },
    mounted() {
        this.fetchTables();
        this.fetchAccounts();
    },
    methods: {
      fetchAccounts () {
          axios.get('/api/accounts')
               .then(response => {
                 this.$store.commit('updateAccountList', response.data.accounts);
               })
              .catch(function (error) {
                 console.log(error);
              });
      },
      fetchTables () {
          axios.get('/api/tables')
               .then(response => {
                   this.$store.commit('updateMesasList', response.data.mesas);
               })
              .catch(function (error) {
                 console.log(error);
              });
      },
      createAccount () {
        axios.defaults.headers.common = {
          'X-Requested-With': 'XMLHttpRequest',
        };

        if(this.$store.state.selectedTable > 0) {
          let accountData = {
            selectedTable: parseInt(this.$store.state.selectedTable)
          };

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
          this.fetchAccounts();
          this.cleanForm();
        } else {
          swal('Error', 'Es necesario especificar valores correctos para procesar la cuenta', 'warning');
        }
      },
      cleanForm () {
        this.$store.commit('updateSelectedTable', 0);
      }
    }
});