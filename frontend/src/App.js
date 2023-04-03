import { Routes, Route } from "react-router-dom";
import Layout from "./components/Layout";
import ProductsListPage from "./pages/ProductsListPage";
import AddProductFormPage from "./pages/AddProductFormPage";

function App() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<ProductsListPage />} />

        <Route path="addproduct">
          <Route index element={<AddProductFormPage />} />
        </Route>
      </Route>
    </Routes>
  );
}

export default App;
