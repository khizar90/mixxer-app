import React, { useEffect, useState } from "react";
import Navbar from "./common/Navbar";
import Footer from "./common/Footer";
import combinedData from "./TermData";

function TermsTab() {
  const [toggleState, setToggleState] = useState(() => {
    const storedValue = sessionStorage.getItem("step");
    return storedValue ? parseInt(storedValue) : 1;
  });

  const toggleTab = (index) => {
    setToggleState(index);
  };

  useEffect(() => {
    sessionStorage.setItem("step", toggleState);
  }, [toggleState]);

  const lineStyle = {
    top:
      toggleState === 1
        ? "30px"
        : toggleState === 2
        ? "78px"
        : toggleState === 3
        ? "125px"
        : toggleState === 4
        ? "175px"
        : "30px",
  };

  localStorage.setItem("termUrl", window.location.href);

  return (
    <>
      <Navbar />
      <div className="container terms">
        <div className="row" style={{ paddingTop: "80px" }}>
          <div className="col-md-4 d-lg-block d-md-block d-sm-flex d-flex align-items-center justify-content-center">
            <div className="shadow p-3" style={{ width: "225px" }}>
              <hr className="hr-2" style={lineStyle} />
              <ul className="d-flex flex-column gap-4">
                <li
                  className={toggleState === 1 ? "active" : ""}
                  onClick={() => toggleTab(1)}
                >
                  Terms of Services
                </li>
                <li
                  className={toggleState === 2 ? "active" : ""}
                  onClick={() => toggleTab(2)}
                >
                  EULA for Mixxer
                </li>
                <li
                  className={toggleState === 3 ? "active" : ""}
                  onClick={() => toggleTab(3)}
                >
                  Privacy Policies
                </li>
                <li
                  className={toggleState === 4 ? "active" : ""}
                  onClick={() => toggleTab(4)}
                >
                  Disclaimer for Mixxer
                </li>
              </ul>
            </div>
          </div>
          <div className="col-md-8 pt-lg-0 pt-md-0 pt-sm-5 pt-5">

            <div className={toggleState === 1 ? "d-block " : "d-none"}>
              <h1 className="mb-4">TERMS OF SERVICES</h1>
              <div className="term-text d-flex flex-column gap-4">
                <p>Effective Date: May 8, 2024</p>
                <small>
                  These Terms of Service ("Terms") govern your access to and use
                  of the Mixxer mobile application (the "App") and related
                  services provided by Mixxer, Inc. ("Mixxer," "we," "us," or
                  "our"). By accessing or using the App, you agree to be bound
                  by these Terms. If you do not agree to these Terms, please do
                  not use the App.
                </small>
                <p>1. Use Of the App</p>
                <small>
                  <span>1.1 Eligibility: </span> You must be at least 18 years
                  old to use the App. If you are under 18 years old, you must
                  have permission from a parent or guardian.
                </small>
                <small>
                  <span>1.2 Account Registration: </span> Account Registration:
                  You agree to provide accurate and complete information when
                  creating your account. You are responsible for maintaining the
                  confidentiality of your account credentials and for all
                  activities that occur under your account.
                </small>
                <small>
                  <span>1.3 Prohibited Conduct: </span> Prohibited Conduct: You
                  agree not to engage in any of the following prohibited
                  activities:
                </small>
                <ul>
                  <li>Violating any applicable laws or regulations.</li>
                  <li>Impersonating another person or entity.</li>
                  <li>
                    Posting harmful, defamatory, or inappropriate content.
                  </li>
                  <li>
                    Attempting to gain unauthorized access to the App or its
                    related systems
                  </li>
                </ul>
                <p>2. User content</p>
                <small>
                  <span>2.1 Ownership: </span>You retain ownership of the
                  content you create and share on the App.
                </small>
                <small>
                  <span>2.2 License: </span>By posting content on the App, you
                  grant Mixxer a non-exclusive, royalty-free, worldwide license
                  to use, modify, reproduce, and distribute your content for the
                  purpose of operating and promoting the App.
                </small>
                {combinedData.items.map((val, index) => (
                  <div key={index} className="d-flex flex-column gap-2">
                    <p className="mb-0">{val.title}</p>
                    <small>{val.description}</small>
                  </div>
                ))}
              </div>
            </div>

            <div className={toggleState === 2 ? "d-block " : "d-none"}>
              <h1>END USER LICENSE AGREEMENT</h1>
              <small>
                This copy of Mixxer ("the Software Product") and accompanying
                documentation is licensed and not sold. This Software Product is
                protected by copyright laws and treaties, as well as laws and
                treaties related to other forms of intellectual property. Mixxer
                Technologies, LLC or its subsidiaries, affiliates, and suppliers
                (collectively "Mixxer") own intellectual property rights in the
                Software Product. The Licensee's ("you" or "your") license to
                download, use, copy, or change the Software Product is subject
                to these rights and to all the terms and conditions of this End
                User License Agreement ("Agreement").
              </small>
              {combinedData.data.map((agree, index) => (
                <div
                  key={index}
                  className="d-flex flex-column gap-3 term-text mb-4 mt-3"
                >
                  <p className="mb-0">{agree.title}</p>
                  <small
                    dangerouslySetInnerHTML={{ __html: agree.description }}
                  ></small>
                </div>
              ))}
            </div>

            <div className={toggleState === 3 ? "d-block " : "d-none"}>
              <h1>PRIVACY POLICIES</h1>

              <div className="term-text d-flex flex-column gap-4">
                <p>Effective Date: May 8, 2024</p>
                <small>
                  Thank you for using Mixxer! This Privacy Policy explains how
                  we collect, use, disclose, and protect your personal
                  information when you use our mobile application (the "App")
                  and related services.
                </small>
                <p>Information we collect</p>
                <small>
                  <span>1. Personal Information: </span> When you create an
                  account, we collect your name, email address, profile picture,
                  and other optional information you choose to provide.
                </small>
                <small>
                  <span>2. User Content: </span> We may collect content that you
                  create or share on Mixxer, including photos, videos, posts,
                  and comments.
                </small>
                <small>
                  <span>3. Usage Information: </span> We automatically collect
                  certain information about your device and how you interact
                  with the App, such as your IP address, device type, browser
                  type, and usage patterns.
                </small>
                <small>
                  <span>4. Location Information: </span> With your consent, we
                  may collect and process your precise location data to provide
                  location-based services.
                </small>

                <p>How we use your Information</p>
                <small>
                  <span>1. To Provide and improve the app: </span>We use your
                  information to operate, maintain, and enhance Mixxer,
                  including developing new features and optimizing user
                  experience
                </small>
                <small>
                  <span>2 To Communicate with you: </span>We may send you
                  service-related announcements, updates, and promotional
                  messages.
                </small>
                <small>
                  <span>2 To our Users: </span>We use your information to detect
                  and prevent fraud, abuse, or security incidents.
                </small>

                <p>Sharing your Information</p>
                <small>
                  <span>1. with third Parties: </span>We may share your
                  information with third-party service providers that assist us
                  in providing and improving our services.
                </small>
                <small>
                  <span>2 Legal Compliance: </span>We may disclose your
                  information if required by law or in response to valid legal
                  requests.
                </small>

                {combinedData.policy.map((privac, index) => (
                  <div
                    key={index}
                    className="d-flex flex-column gap-3 term-text mb-4"
                  >


                    <p className="mb-0">{privac.title}</p>
                    <small>{privac.description}</small>
                  </div>
                ))}
              </div>
            </div>

            <div className={toggleState === 4 ? "d-block " : "d-none"}>
              <h1>DISCLAIMER FOR MIXXER</h1>

              {combinedData.disclaimer.map((disc, index) => (
                <div
                  key={index}
                  className="d-flex flex-column gap-3 term-text mb-4"
                >
                  <p className="mb-0">{disc.title}</p>
                  <small
                    dangerouslySetInnerHTML={{ __html: disc.description }}
                  ></small>
                </div>
              ))}
            </div>

          </div>
        </div>
      </div>
      <Footer setToggleState={setToggleState} />
    </>
  );
}

export default TermsTab;
