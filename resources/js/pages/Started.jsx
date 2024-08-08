import React from "react";

import playstore from "../assets/Images/playstore.png";
import appstore from "../assets/Images/appstore.png";

import starter from "../assets/Images/starter.png";

function Started() {
  return (
    <div className="container start-bg mt-5">
      <div className="row">
        <div className="col-md-6 col-sm-6 col-6 d-flex justify-content-center align-items-center p-0">
          <img src={starter} className="w-100" />
        </div>

        <div className="col-md-6 col-sm-6 col-6 d-flex flex-column justify-content-center">
          <div className="d-flex flex-column justify-content-center gap-3">
            <h3>Get Started with MIXXER</h3>
            <h5>
              Create, Discover, and Join
              <span> Mixxer. Download Mixxer Now To Get Started!</span>
            </h5>
          </div>
          <div className="d-flex gap-3 marg-img">
            <img src={playstore} />
            <img src={appstore} />
          </div>
          <p>*Experience the next level of social networking.</p>
        </div>
        
      </div>
    </div>
  );
}

export default Started;
