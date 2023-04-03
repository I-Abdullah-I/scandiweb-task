import { Outlet } from "react-router-dom";
import Footer from "./Footer";

const Layout = () => {
  return (
      <main className="App">
        <Outlet />
        <Footer />
      </main>
  );
};

export default Layout;
