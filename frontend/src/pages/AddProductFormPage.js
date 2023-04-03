import "./AddProductForm.css";

import { useNavigate } from "react-router-dom";
// import { useEffect, useState } from "react";
import { useDispatch } from "react-redux";

import { addProduct } from "../features/products/productsSlice";

import { useFormik } from "formik";
import * as Yup from "yup";
import axios from "axios";

const AddProductFormPage = () => {
  const navigate = useNavigate();
  const dispatch = useDispatch();
  // const [onSkuChange, setOnSkuChange] = useState(false);

  const validateUniqueSKU = async (value) => {
    const response = await axios.get(
      `${process.env.REACT_APP_BASE_URL}/product/fetch`,
      {
        params: {
          sku: value,
        },
      }
    );
    return !response.data;
  };

  const formik = useFormik({
    initialValues: {
      sku: "",
      name: "",
      price: "",
      productType: "",
      size: "",
      height: "",
      width: "",
      length: "",
      weight: "",
    },
    validationSchema: Yup.object({
      sku: Yup.string()
        .required("Required")
        .test({
          name: "is_unique",
          message: "Another product is associated with the same sku.",
          test: async (val, ctx) => {
            const flag = await validateUniqueSKU(val);
            // console.log(ctx);
            return flag;
          },
          exclusive: true}
        ),
      name: Yup.string().required("Required"),
      price: Yup.number().required("Required").positive(),
      productType: Yup.string().required("Required"),
      size: Yup.number().when("productType", {
        is: (val) => val === "DVD",
        then: () => Yup.number().positive().required("Required"),
        otherwise: () => Yup.number().notRequired(),
      }),
      weight: Yup.number().when("productType", {
        is: (val) => val === "Book",
        then: () => Yup.number().positive().required("Required"),
      }),
      height: Yup.number().when("productType", {
        is: (val) => val === "Furniture",
        then: () => Yup.number().positive().required("Required"),
        otherwise: () => Yup.number().notRequired(),
      }),
      width: Yup.number().when("productType", {
        is: (val) => val === "Furniture",
        then: () => Yup.number().positive().required("Required"),
        otherwise: () => Yup.number().notRequired(),
      }),
      length: Yup.number().when("productType", {
        is: (val) => val === "Furniture",
        then: () => Yup.number().positive().required("Required"),
        otherwise: () => Yup.number().notRequired(),
      }),
    }),
  });

  const DVDExtension = (
    <>
      <label htmlFor="size">Size (MB)</label>
      <div className="col2/3">
        <input id="size" type="text" {...formik.getFieldProps("size")} />
        {formik.touched.size && formik.errors.size ? (
          <div className="errorText">{formik.errors.size}</div>
        ) : null}
        <p>Please provide the size of the DVD disc in MB.</p>
      </div>
    </>
  );

  const BookExtension = (
    <>
      <label htmlFor="weight">Weight (KG)</label>
      <div className="col2/3">
        <input id="weight" type="text" {...formik.getFieldProps("weight")} />
        {formik.touched.weight && formik.errors.weight ? (
          <div className="errorText">{formik.errors.weight}</div>
        ) : null}
        <p>Please provide the weight of the book in KG.</p>
      </div>
    </>
  );

  const FurnitureExtension = (
    <>
      <label htmlFor="height">Height (CM)</label>
      <div className="col2/3">
        <input id="height" type="text" {...formik.getFieldProps("height")} />
        {formik.touched.height && formik.errors.height ? (
          <div className="errorText">{formik.errors.height}</div>
        ) : null}
      </div>

      <label htmlFor="width">Width (CM)</label>
      <div className="col2/3">
        <input id="width" type="text" {...formik.getFieldProps("width")} />
        {formik.touched.width && formik.errors.width ? (
          <div className="errorText">{formik.errors.width}</div>
        ) : null}
      </div>

      <label htmlFor="length">Length (CM)</label>
      <div className="col2/3">
        <input id="length" type="text" {...formik.getFieldProps("length")} />
        {formik.touched.length && formik.errors.length ? (
          <div className="errorText">{formik.errors.length}</div>
        ) : null}
        <p>Please provide the dimensions in HxWxL format.</p>
      </div>
    </>
  );

  const saveProduct = async () => {
    // console.log(formik.errors);
    let payload = formik.values;
    if (formik.isValid) {
      const attributes = [
        "size",
        "weight",
        "height",
        "width",
        "length",
        "productType",
      ];
      if (payload["productType"] === "DVD") {
        payload = { ...payload, attributes: { size: +payload["size"] } };
      } else if (payload["productType"] === "Book") {
        payload = { ...payload, attributes: { weight: +payload["weight"] } };
      } else if (payload["productType"] === "Furniture") {
        payload = {
          ...payload,
          attributes: {
            height: +payload["height"],
            width: +payload["width"],
            length: +payload["length"],
          },
        };
      }
      payload = { ...payload, type: payload["productType"] };
      payload["price"] = +payload["price"];
      payload = Object.fromEntries(
        Object.entries(payload).filter(([k, _]) => !attributes.includes(k))
      );

      try {
        await dispatch(addProduct(payload)).unwrap();
        navigate("/");
      } catch (error) {
        console.error(error);
      }
    }
  };

  return (
    <>
      <div className="header">
        <div className="nav">
          <h1>Product Add</h1>
          <div className="actionButtons">
            <button type="button" onClick={() => saveProduct()}>
              Save
            </button>
            <button type="button" onClick={() => navigate("/")}>
              Cancel
            </button>
          </div>
        </div>
        <hr />
      </div>
      <section className="addProductForm">
        <form id="product_form">
          <label htmlFor="sku">SKU</label>
          <div className="col2/3">
            <input
              id="sku"
              type="text"
              autoFocus
              {...formik.getFieldProps("sku")}
            />
            {formik.touched.sku && formik.errors.sku ? (
              <div className="errorText">{formik.errors.sku}</div>
            ) : null}
          </div>

          <label htmlFor="name">Name</label>
          <div className="col2/3">
            <input id="name" type="text" {...formik.getFieldProps("name")} />
            {formik.touched.name && formik.errors.name ? (
              <div className="errorText">{formik.errors.name}</div>
            ) : null}
          </div>

          <label htmlFor="price">Price ($)</label>
          <div className="col2/3">
            <input id="price" type="text" {...formik.getFieldProps("price")} />
            {formik.touched.price && formik.errors.price ? (
              <div className="errorText">{formik.errors.price}</div>
            ) : null}
          </div>

          <label htmlFor="productType" className="selector">
            Type Switcher
          </label>
          <div className="selectorDiv">
            <select id="productType" {...formik.getFieldProps("productType")}>
              <option value=""></option>
              <option id="DVD" value="DVD">
                DVD-disc
              </option>
              <option id="Book" value="Book">
                Book
              </option>
              <option id="Furniture" value="Furniture">
                Furniture
              </option>
            </select>
            {formik.touched.productType && formik.errors.productType ? (
              <div className="errorText">{formik.errors.productType}</div>
            ) : null}
          </div>

          {formik.values.productType === "DVD"
            ? DVDExtension
            : formik.values.productType === "Book"
            ? BookExtension
            : formik.values.productType === "Furniture"
            ? FurnitureExtension
            : null}
        </form>
      </section>
    </>
  );
};

export default AddProductFormPage;
