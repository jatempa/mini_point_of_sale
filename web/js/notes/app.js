let productForm = {
    template: `
    <section>
      <div v-if="selectedAccount > 0" class="field" style="margin-top: 8px;">
        <label class="label">Selecciona un tipo de producto</label>
        <div class="control">
          <div class="select">
            <select v-model="selectedCategory">
              <option v-for="category in categories" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
        </div>
      </div>
      <div v-if="selectedCategory > 0" class="field" style="margin-top: 8px;">
        <label class="label">Selecciona un producto</label>
        <div class="control">
          <div class="select">
            <select v-model="selectedProduct">
              <option v-for="product in getProductsByCategory" :value="product.id">
                {{ product.name }} ($ {{ product.price }})
              </option>
            </select>
          </div>
        </div>
      </div>
      <div v-if="selectedProduct > 0" style="margin-top: 8px;">
        <div class="field">
          <label class="label">Cantidad</label>
          <div class="control">
            <input v-model="amount" class="input" type="number" min="0" max="100" placeholder="Introduce la cantidad del producto">
          </div>
        </div>
        <button class="button is-info" @click.prevent="addProduct">
          <span class="icon is-normal">
              <i class="fa fa-plus"></i>
          </span>
          <span>Agregar a lista</span>
        </button>
      </div>      
    </section>                   
    `,
    data() {
      return {
        categories: [],
        products: [],
        amount: 0
      }
    },
    mounted() {
      this.fetchCategories();
      this.fetchProducts();
    },
    methods: {
      fetchCategories () {
        axios.get('/api/categories')
             .then(response => {
               this.categories = response.data.categories;
             })
             .catch(function (error) {
               console.log(error);
             });
      },
      fetchProducts () {
        axios.get('/api/products')
             .then(response => {
               this.products = response.data.products
             })
             .catch(function (error) {
               console.log(error);
             });
      },
      addProduct () {
        if (parseInt(this.amount) > 0) {
          let product = {
              amount: parseInt(this.amount)
          };

          axios.get('/api/products/' + parseInt(this.selectedProduct))
               .then(response => {
                  product.id = response.data.product.id;
                  product.name = response.data.product.name;
                  product.price = parseFloat(response.data.product.price);
                  this.$store.commit('addProductToList', product);
               })
               .catch(function (error) {
                  console.log(error);
              });
          // Clear
          this.$store.commit('updateSelectedCategory', 0);
          this.$store.commit('updateSelectedProduct', 0);
          this.amount = 0;
        } else {
          swal('Error', 'Ingresa una cantidad mínima de 1 para continuar', 'warning');
        }
      }
    },
    computed: {
      getProductsByCategory () {
        return this.products.filter((p) => p.category === this.selectedCategory);
      },
      selectedAccount: {
        get () {
          return this.$store.state.selectedAccount
        },
        set (value) {
          this.$store.commit('updateSelectedAccount', value)
        }
      },
      selectedCategory: {
        get () {
          return this.$store.state.selectedCategory
        },
        set (value) {
          this.$store.commit('updateSelectedCategory', value)
        }
      },
      selectedProduct: {
        get () {
          return this.$store.state.selectedProduct
        },
        set (value) {
          this.$store.commit('updateSelectedProduct', value)
        }
      }
    }
};

let productList = {
  template: `
  <section>
    <div class="control">
      <label class="label">Selecciona una cuenta</label>
      <div class="select">
        <select v-model="selectedAccount">
          <option v-for="account in accounts" :value="account.id" v-if="account.status">
            Cuenta {{ account.id }} - {{ account.mesa }}
          </option>
        </select>
      </div>
    </div>
    <div v-if="carProductListLength > 0" class="field" style="margin-top: 8px;">
      <div class="label">Lista de productos</div>
      <div class="control">
        <table class="table">
          <thead>
            <tr>
              <th>No.</th>
              <th>Producto</th>
              <th>Precio</th>
              <th>Cant.</th>
              <th>Subt.</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th colspan="4">Total $</th>
              <th>{{ totalCost }}</th>
            </tr>
          </tfoot>
          <tbody>
            <tr v-for="(product, index) in getProducts" :key="product.id">
              <td>{{ index + 1 }}</td>
              <td>{{ product.name }}</td>
              <td>{{ product.price }}</td>
              <td>{{ product.amount }}</td>
              <td>{{ product.amount * product.price }}</td>
            </tr>
          </tbody>
        </table>    
      </div>    
    </div>  
  </section>
  `,
  data() {
    return {
      accounts: []
    }
  },
  mounted() {
    this.fetchAccounts();
  },
  methods: {
    fetchAccounts() {
      axios.get('/api/accounts')
           .then(response => {
              this.accounts = response.data.accounts;
           })
           .catch(function (error) {
              console.log(error);
           });
    }
  },
  computed: {
    getProductById (product_id) {
      return this.products.filter((p) => p.id === product_id);
    },
    getProducts () {
      return this.$store.state.carProducts;
    },
    carProductListLength () {
      return this.$store.state.carProducts.length;
    },
    totalCost () {
      return this.$store.state.carProducts.reduce((acc, x) => acc + (x.price * x.amount), 0);
    },
    selectedAccount: {
      get () {
          return this.$store.state.selectedAccount
      },
      set (value) {
          this.$store.commit('updateSelectedAccount', value)
      }
    }
  }
};

Vue.use('vuex');

const store = new Vuex.Store({
    state: {
      lastNumberNote: 0,
      selectedAccount: 0,
      selectedCategory: 0,
      selectedProduct: 0,
      carProducts: []
    },
    mutations: {
      updateLastNumberNote(state) {
        state.lastNumberNote += 1;
      },
      updateSelectedAccount(state, selectedAccount) {
        state.selectedAccount = selectedAccount;
      },
      updateSelectedCategory(state, selectedCategory) {
        state.selectedCategory = selectedCategory;
      },
      updateSelectedProduct(state, selectedProduct) {
        state.selectedProduct = selectedProduct;
      },
      addCarProducts(state, product) {
        state.carProducts.push(product);
      },
      resetCarProducts(state) {
        state.carProducts = [];
      }
    }
});

new Vue({
  delimiters: ['${', '}'],
  el: 'main',
  store,
  components: { productList, productForm },
  mounted() {
    this.getLastNoteNumber();
  },
  methods: {
    getLastNoteNumber () {
      axios.get('/api/notes/lastNoteId')
           .then(response => {
             response.data.map((n) => this.$store.state.lastNumberNote = parseInt(n.numberNote) + 1);
             if (!(this.$store.state.lastNumberNote > 0)) {
               this.$store.state.lastNumberNote = 1;
             }
           })
           .catch(function (error) {
             console.log(error);
             this.$store.state.lastNumberNote = 1;
           });
    },
    createNote () {
      axios.defaults.headers.common = {
        'X-Requested-With': 'XMLHttpRequest',
      };

      if(this.$store.state.products.length > 0) {
        let noteData = {
          selectedAccount: parseInt(this.$store.state.selectedAccount),
          numberNote: parseInt(this.$store.state.lastNumberNote),
          products: this.$store.state.products
        };

        axios.post('/api/notes/create', noteData)
          .then(function (response) {
            if(response.data === 'success') {
              swal('¡Correcto!', 'Comanda registrada satisfactoriamente', 'success');
            }
          })
          .catch(function (error) {
            console.log(error);
              swal('Error', 'Esta comanda no pudo ser registrada en el sistema', 'error')
          });
          // Increment number note
          this.$store.commit('updateLastNumberNote');
          // Clean form
          this.cleanForm();
      } else {
        swal('Error', 'Es necesario especificar valores correctos para procesar la comanda', 'warning');
      }
    },
    cleanForm () {
      this.$store.commit('updateSelectedAccount',0);
      this.$store.commit('updateSelectedCategory', 0);
      this.$store.commit('updateSelectedProduct', 0);
      this.$store.commit('resetCarProducts');
    }
  }
});