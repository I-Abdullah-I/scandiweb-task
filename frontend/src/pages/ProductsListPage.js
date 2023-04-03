import "./ProductsListPage.css";

import { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from "react-router-dom";

import ProductExcerpt from "../features/products/ProductExcerpt";
import {
  fetchProducts,
  massDelete,
  selectAllProducts,
  getStatus,
} from "../features/products/productsSlice";

const ProductsListPage = () => {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const products = useSelector(selectAllProducts);
  const status = useSelector(getStatus);
  const [deleteList, setDeleteList] = useState([]);
  const [deleteRequestStatus, setDeleteRequestStatus] = useState("idle");

  const setDeleteListWrapper = (value, action) => {
    if (action === "set") {
      setDeleteList(() => deleteList.concat(value));
    } else if (action === "unset") {
      setDeleteList(() => deleteList.filter((sku) => sku !== value));
    }
  };

  const massDeleteHandler = async () => {
    if (deleteList.length && deleteRequestStatus === "idle") {
      try {
        setDeleteRequestStatus("pending");
        await dispatch(massDelete(deleteList)).unwrap();
      } catch (error) {
        setDeleteRequestStatus("failed");
        console.error(error);
      } finally {
        setDeleteRequestStatus("idle");
      }
    }
  };

  useEffect(() => {
    if (status === "idle") {
      dispatch(fetchProducts());
    }
  }, [status, dispatch]);

  const sortedProducts = Object.values(products)
    .sort((a, b) => {
      return new Date(a.created_at) - new Date(b.created_at);
    })
    .reverse();

  const renderedProducts = sortedProducts.map((product) => (
    <ProductExcerpt
      key={product.id}
      id={product.id}
      SKU={product.sku}
      name={product.name}
      price={product.price}
      type={product.type}
      description={product.attributes}
      checkHandler={setDeleteListWrapper}
    />
  ));

  return (
    <>
      <div className="header">
        <div className="nav">
          <h1>Product List</h1>
          <div className="actionButtons">
            <button type="button" onClick={() => navigate("addproduct")}>
              Add
            </button>
            <button type="button" onClick={() => massDeleteHandler()}>
              Mass Delete
            </button>
          </div>
        </div>
        <hr />
      </div>
      <div className="mainContainer">{renderedProducts}</div>
    </>
  );
};

export default ProductsListPage;
