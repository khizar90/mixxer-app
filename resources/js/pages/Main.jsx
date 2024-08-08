import React, { useEffect } from "react";
import { useLocation } from "react-router-dom";

import Navbar from "../components/common/Navbar";
import Home from "./Home";
import About from "./About";
import Essential from "./Essential";
import Explore from "./Explore";
import Discover from "./Discover";
import Started from "./Started";
import FAQ from "./FAQ";
import Contact from "./Contact";
import Footer from "../components/common/Footer";

function Main() {
  const location = useLocation();

  const handleScroll = (sectionId) => {
    const section = document.getElementById(sectionId);
    if (section) {
      section.scrollIntoView({ behavior: "smooth" });
    }
  };

  useEffect(() => {
    if (location.state && location.state.section) {
      handleScroll(location.state.section);
    }
  }, [location]);

  return (
    <div>
      <Navbar handleScroll={handleScroll} />
      <div id="home">
        <Home />
      </div>
      <div id="about">
        <About />
      </div>
      <Essential />
      <div id="feature">
        <Explore />
      </div>
      <Discover />
      <Started />
      <div id="faq">
        <FAQ />
      </div>
      <div id="contact">
        <Contact />
      </div>
      <Footer handleScroll={handleScroll} />
    </div>
  );
}

export default Main;
