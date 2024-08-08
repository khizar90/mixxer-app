import React, { useState } from "react";

import appstore from "../assets/Images/appstore.png";
import playstore from "../assets/Images/playstore.png";
import bg from "../assets/Images/bg.png";

function Home() {
  return (
    <div className="home-bg">
      <img src={bg} alt="HOME" />
      <div
        className="container"
        style={{ position: "relative", zIndex: "100" }}
      >
        <div className="d-flex flex-column gap-lg-4 gap-md-3 gap-sm-2 gap-2 text-white">
          <h1>Ready to amplify your social life?</h1>
          <p className="mb-0">
            Experience the next level of social networking with Mixxer. <br />
            Download Mixxer now and let the adventure begin!
          </p>
          <button className="p-2 mb-lg-3 mb-md-3 mb-sm-0 mb-0">
            Download the app
          </button>
          <div className="d-flex flex-column gap-2">
            <img src={playstore} />
            <img src={appstore} />
          </div>
        </div>

      </div>
    </div>
  );
}

export default Home;
