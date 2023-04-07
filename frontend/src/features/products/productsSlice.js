import { createAsyncThunk, createSlice } from "@reduxjs/toolkit";
import axios from "axios";

const READ_PRODUCT_LIST_URL = `${process.env.REACT_APP_BASE_URL}/product/list`;
const ADD_PRODUCT_URL = `${process.env.REACT_APP_BASE_URL}/product/add`;
const MASS_DELETE_PRODUCTS_URL = `${process.env.REACT_APP_BASE_URL}/product/massDelete`;

const initialState = {
  products: [],
  status: "idle",
  error: null,
};

export const fetchProducts = createAsyncThunk(
  "products/fetchProducts",
  async () => {
    const response = await axios.get(READ_PRODUCT_LIST_URL);
    return response.data;
  }
);

export const addProduct = createAsyncThunk(
  "products/addProduct",
  async (payload) => {
    const response = await axios.post(ADD_PRODUCT_URL, payload);
    return response.data;
  }
);

export const massDelete = createAsyncThunk(
  "products/massDelete",
  async (payload) => {
    const response = await axios.delete(MASS_DELETE_PRODUCTS_URL, {
      data: payload,
    });
    return response.data;
  }
);

const productsSlice = createSlice({
  name: "products",
  initialState,
  reducers: {},
  extraReducers(builder) {
    builder
      .addCase(fetchProducts.pending, (state, action) => {
        state.status = "loading";
      })
      .addCase(fetchProducts.fulfilled, (state, action) => {
        state.status = "succeeded";
        let products = [];
        for (const key in action.payload) {
          products.push({ ...action.payload[key] });
        }
        state.products = state.products.concat(products);
      })
      .addCase(fetchProducts.rejected, (state, action) => {
        state.status = "failed";
        state.error = action.error.message;
      })
      .addCase(addProduct.fulfilled, (state, action) => {
        state.status = "succeeded";
        state.products.push(action.payload);

        const sortedProducts = state.products
        .sort((a, b) => {
          if(a.id > b.id) return 1;
          else if(a.id < b.id) return -1;
          else return 0;
        }).reverse();
        state.products = sortedProducts;
      })
      .addCase(addProduct.rejected, (state, action) => {
        state.status = "failed";
        state.error = action.error.message;
      })
      .addCase(massDelete.fulfilled, (state, action) => {
        state.status = "succeeded";
        action.payload.forEach((deletedProduct) => {
          const products = state.products.filter(
            (product) => product.id !== deletedProduct.id
          );
          state.products = products;
        });
      })
      .addCase(massDelete.rejected, (state, action) => {
        state.status = "failed";
        state.error = action.error.message;
      });
  },
});

export const selectAllProducts = (storeStates) => storeStates.products.products;
export const getStatus = (storeStates) => storeStates.products.status;

export default productsSlice.reducer;
