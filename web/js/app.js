let productForm = {
    template: `
    <section>
      <div class="field">
        <label class="label">Categoría de producto</label>
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
      <div v-if="selectedCategory > 0" class="field">
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
      <div v-if="selectedProduct > 0">
        <div class="field">
          <div class="field">
            <label class="label">Cantidad</label>
            <div class="control">
              <input v-model="amount" class="input" type="number" min="0" max="100" placeholder="Introduce la cantidad del producto">
            </div>
          </div>   
        </div>
        <button class="button is-info" @click.prevent="addProduct">
          <span class="icon is-normal">
              <i class="fa fa-plus"></i>
          </span>
          <span>Agregar Producto</span>
        </button>
      </div>      
    </div>                   
    `,
    data() {
      return {
        categories: [],
        products: [],
        selectedCategory: 0,
        selectedProduct: 0,
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
          // Clean Form
          this.cleanForm();
        } else {
          swal('Error', 'Ingresa una cantidad mínima de 1 para continuar', 'warning');
        }
      },
      cleanForm () {
        this.selectedCategory = 0;
        this.selectedProduct = 0;
        this.amount = 0;
      }
    },
    computed: {
      getProductsByCategory () {
        return this.products.filter((p) => p.category === this.selectedCategory);
      }
    }
};

let productList = {
  template: `
    <div v-show="productListLength > 0" class="field">
        <label class="label">Lista de productos</label>
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
  `,
  computed: {
    getProducts() {
      return this.$store.state.products;
    },
    productListLength() {
      return this.$store.state.products.length;
    },
    totalCost() {
      return this.$store.state.products.reduce((acc, x) => acc + (x.price * x.amount), 0);
    }
  }
};

let note = {
    template: `
    <div class="field">
      <label class="label">Número de la mesa</label>
      <div class="control">
        <input v-model="tableNumber" class="input" type="number" min="0" max="100" placeholder="Introduce el número de la mesa">
      </div>
    </div>
    `,
    computed: {
      tableNumber: {
        get () {
          return this.$store.state.tableNumber
        },
        set (tableNumber) {
          this.$store.commit('updateTableNumber', tableNumber)
        }
      }
    }
};

Vue.use('vuex');

const store = new Vuex.Store({
    state: {
      lastNumberNote: 0,
      tableNumber: 0,
      selectedCategory: 0,
      selectedProduct: 0,
      products: [],
      amount: 0
    },
    mutations: {
      updateLastNumberNote(state) {
        state.lastNumberNote += 1;
      },
      updateTableNumber(state, tableNumber) {
        state.tableNumber = tableNumber;
      },
      updateSelectedCategory(state, selectedCategory) {
        state.selectedCategory = selectedCategory;
      },
      updateSelectedProduct(state, selectedProduct) {
        state.selectedProduct = selectedProduct;
      },
      addProductToList(state, product) {
        state.products.push(product);
      }
    }
});

new Vue({
  delimiters: ['${', '}'],
  el: 'main',
  store,
  components: { note, productList, productForm },
  mounted() {
    this.getLastNoteNumber();
  },
  methods: {
    getLastNoteNumber: function () {
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
    cleanForm: function () {
      this.$store.commit('updateTableNumber', 0);
      this.$store.commit('updateSelectedCategory', 0);
      this.$store.commit('updateSelectedProduct', 0);
    }
  }
});