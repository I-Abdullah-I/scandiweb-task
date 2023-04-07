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

  const setDeleteListWrapper = (value, action) => {
    if (action === "set") {
      setDeleteList(() => deleteList.concat(value));
    } else if (action === "unset") {
      setDeleteList(() => deleteList.filter((sku) => sku !== value));
    }
  };

  const massDeleteHandler = async () => {
    if (deleteList.length) {
      try {
        await dispatch(massDelete(deleteList)).unwrap();
      } catch (error) {
        console.error(error);
      } finally {
        setDeleteList([]);
      }
    } else if (deleteList.length === 0) {
      const manualDeleteList = [];
      const checkboxes = document.querySelectorAll(".delete-checkbox");
      checkboxes.forEach((checkbox) => {
        manualDeleteList.push(+checkbox.parentElement.id);
      });
      try {
        await dispatch(massDelete(manualDeleteList)).unwrap();
      } catch (error) {
        console.error(error);
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
            <button
              name="ADD"
              type="submit"
              onClick={() => navigate("addproduct")}
            >
              ADD
            </button>
            <button
              name="MASS DELETE"
              type="submit"
              onClick={() => massDeleteHandler()}
            >
              MASS DELETE
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
