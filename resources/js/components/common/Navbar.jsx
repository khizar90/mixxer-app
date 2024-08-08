import React, { useState, useEffect } from "react";
import { useNavigate, useLocation } from "react-router-dom";
import whitelogo from "../../assets/Images/whitelogo.png";
import logo from "../../assets/Images/logo.png";
import { FaBars } from "react-icons/fa6";
import { IoClose } from "react-icons/io5";
import Collapse from "react-bootstrap/Collapse";

function Navbar() {
  const [isScrolled, setIsScrolled] = useState(false);
  const [show, setShow] = useState(false);
  const termUrl = window.location.href === localStorage.getItem("termUrl");
  const navigate = useNavigate();
  const location = useLocation();

  useEffect(() => {
    const onScroll = () => {
      setIsScrolled(window.scrollY > 50);
    };
    window.addEventListener("scroll", onScroll);
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    if (location.pathname === "/") {
      window.scrollTo(0, 0);
    }
  }, [location.pathname]);

  const handleScroll = (section) => {
    navigate("/", { state: { section } });
  };

  const [navbarClass, setNavbarClass] = useState("");
  const [textClass, setTextClass] = useState("");

  useEffect(() => {
    if (isScrolled && !show) {
      setNavbarClass("bg-white shadow");
      setTextClass("text-secondary");
    } else if (show) {
      setNavbarClass("bg-brown");
      setTextClass("text-white");
    } else if (termUrl) {
      setNavbarClass("bg-white shadow");
      setTextClass("text-secondary");
    } else {
      setNavbarClass("bg-transparent");
      setTextClass("text-white");
    }
  }, [isScrolled, show, termUrl]);

  return (
    <>
      <nav
        className={`nav-bar ${navbarClass} fixed-top py-lg-3 py-md-2 py-sm-0 py-0`}
      >
        <div className="container d-flex align-items-center justify-content-between">
          <img
            src={(isScrolled && !show) || (termUrl && !show) ? logo : whitelogo}
            alt="LOGO"
            onClick={() => {
              navigate("/");
              window.scrollTo(0, 0);
            }}
            style={{ cursor: "pointer" }}
          />
          <div
            className={`d-lg-flex d-md-flex d-sm-none d-none align-items-center gap-lg-5 gap-md-4 gap-sm-4 ${textClass} gap-4 `}
          >
            {["home", "about", "feature", "faq", "contact"].map(
              (section, index) => (
                <p
                  key={index}
                  className="mb-0"
                  onClick={() => handleScroll(section)}
                >
                  {section.charAt(0).toUpperCase() + section.slice(1)}
                </p>
              )
            )}
          </div>
          {show ? (
            <IoClose
              className={`d-lg-none d-md-none d-sm-flex d-flex ${textClass} mt-3 h3`}
              onClick={() => setShow(false)}
            />
          ) : (
            <FaBars
              className={`d-lg-none d-md-none d-sm-flex d-flex ${textClass} mt-3 h3`}
              onClick={() => setShow(true)}
            />
          )}
        </div>

        <div>

          <Collapse in={show}>
            <div>
              <div
                className="d-flex flex-column gap-4 text-white"
                style={{ backgroundColor: "#7E6C54", padding: "20px 15px 0" }}
              >
                {["home", "about", "feature", "faq", "contact"].map(
                  (section, index) => (
                    <div key={index} className="d-flex flex-column">
                      <p
                        onClick={() => {
                          handleScroll(section);
                          setShow(false);
                        }}
                      >
                        {section.charAt(0).toUpperCase() + section.slice(1)}
                      </p>
                      <hr className="side-hr" />
                    </div>
                  )
                )}
              </div>
            </div>
          </Collapse>

        </div>
      </nav>
    </>
  );
}

export default Navbar;
