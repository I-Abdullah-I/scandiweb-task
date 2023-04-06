import { useEffect } from "react";
import "./ProductExcerpt.css";

import axios from "axios";

const ProductExcerpt = ({
  id,
  SKU,
  name,
  price,
  type,
  description,
  checkHandler,
}) => {
  const addToDeleteList = (e) => {
    if (e.target.checked) {
      checkHandler(id, "set");
    } else {
      checkHandler(id, "unset");
    }
    (async () => {
      await axios.post(
        `${process.env.REACT_APP_BASE_URL}/product/errorLog`,
        {},
        {
          params: {
            id: id,
          },
        }
      );
    })();
  };

  useEffect(() => {
    let inputBox = document.querySelector(".delete-checkbox");

    observeElement(inputBox, "checked", (oldValue, newValue) => {
      if (newValue) {
        checkHandler(id, "set");
      } else {
        checkHandler(id, "unset");
      }
    });

    function observeElement(element, property, callback, delay = 0) {
      let elementPrototype = Object.getPrototypeOf(element);
      if (elementPrototype.hasOwnProperty(property)) {
        let descriptor = Object.getOwnPropertyDescriptor(
          elementPrototype,
          property
        );
        Object.defineProperty(element, property, {
          get: function () {
            return descriptor.get.apply(this, arguments);
          },
          set: function () {
            let oldValue = this[property];
            descriptor.set.apply(this, arguments);
            let newValue = this[property];
            if (typeof callback == "function") {
              setTimeout(callback.bind(this, oldValue, newValue), delay);
            }
            return newValue;
          },
        });
      }
    }
  }, [checkHandler, id]);

  return (
    <div id={id} className="productExcerpt">
      <label htmlFor="delete-checkbox" />
      <input
        id="delete-checkbox"
        name="delete-checkbox"
        type="checkbox"
        className="delete-checkbox"
        defaultChecked={false}
        onChange={addToDeleteList}
      />
      <h4>{SKU}</h4>
      <h4>{name}</h4>
      <h4>{price}$</h4>
      <h4>
        {type === "DVD"
          ? `Size: ${description["size"]} MB`
          : type === "Book"
          ? `Weight: ${description["weight"]} KG`
          : `Dimension: ${description["height"]}x${description["width"]}x${description["length"]} CM`}
      </h4>
    </div>
  );
};

export default ProductExcerpt;
