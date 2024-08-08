import React from "react";

import exportphone from "../assets/Images/explore-phone.png";

function Explore() {
  return (
    <div className="explore-bg">
      <div className="p-5 position-relative pb-0">
        <h1>MIXXER ESSENTIALS:</h1>
        <h1>YOUR GUIDE TO THE HOME PAGE</h1>
        <img src={exportphone} />

        <div className="d-flex flex-column gap-lg-3 gap-md-3 gap-sm-2 gap-1 position-absolute feature-text text-white">
          <h5>
            <span>Featured Mixxers:</span> Curated For You
          </h5>
          <p className="mb-0 pe-lg-4 pe-md-0 pe-sm-0 pe-0">
            Discover popular suggestions tailored to your interests and
            preferences. Explore Mixxers from boosted users and trending
            activities to join exciting and highly-rated gatherings.
          </p>
        </div>

        <div className="d-flex flex-column gap-lg-3 gap-md-3 gap-sm-2 gap-1 position-absolute nearby-text text-white">
          <h5>
            <span>Nearby Mixxers:</span> Local Fun
          </h5>
          <p className="mb-0 pe-lg-5 pe-md-0 pe-sm-0 pe-0">
            Find Mixxers happening in your area and connect with local groups.
            Easily locate gatherings close to you and participate in nearby
            activities for a convenient and fun social experience.
          </p>
        </div>

        <div className="d-flex flex-column gap-lg-3 gap-md-3 gap-sm-2 gap-1 position-absolute friend-text text-white">
          <h5>
            <span>Friend Mixxers:</span> Stay in the Loop
          </h5>
          <p className="mb-0 pe-lg-3 pe-md-0 pe-sm-0 pe-0">
            Access Mixxers created by users you are connected with. Keep up with
            your friends’ activities, join their planned outings, and enjoy
            socializing with people you know and trust.
          </p>
        </div>
      </div>
    </div>
  );
}

export default Explore;
