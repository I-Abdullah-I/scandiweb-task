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

    const req = async() => {
      const response = await axios.post(
        `${process.env.REACT_APP_BASE_URL}/product/errorLog`,
        {
          params: {
            id: id,
          },
        }
      );
    };
  };

  return (
    <div id={id} className="productExcerpt">
      <input
        id="delete-checkbox"
        name="delete-checkbox"
        type="checkbox"
        className="delete-checkbox"
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
