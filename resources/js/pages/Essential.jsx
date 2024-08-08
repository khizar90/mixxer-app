import React from "react";

import essential from "../assets/Images/essential.png";
import { FaCheck } from "react-icons/fa6";

function Essential() {
  const items = [
    {
      title: "Personalized Outings:",
      description:
        "Tailor one-on-one or small group outings to your unique interests and schedule. From spontaneous coffee dates to planned weekend hikes, Mixxer ensures every outing is meaningful and enjoyable.",
    },
    {
      title: "Interest-Based connections:",
      description:
        "Facilitate platonic relationships across genders by connecting with individuals who share your passions. Mixxer's advanced matching algorithm helps you find like-minded people, making it easier to form deep, meaningful friendships.",
    },
    {
      title: "Effortless coordination:",
      description:
        "Eliminate group chat confusion. Mixxer simplifies organizing get-togethers, ensuring everyone is on the same page. Focus on having fun while we handle the logistics.",
    },
    {
      title: "Cost-Effective Experience:",
      description:
        "Enjoy Mixxer without hidden fees or high subscription costs. Our affordable solution makes it easy to maintain an active social life without financial strain.",
    },
    {
      title: "Community-driven Features:",
      description:
        "Benefit from features developed based on user feedback. Mixxer listens to its community, ensuring a continually improving experience tailored to your socializing needs.",
    },
  ];

  return (
    <div>
      <div className="container" style={{marginTop: '80px', marginBottom: '80px'}}>
        <div className="row essen">
          <div className="col-md-6 text-align-center d-flex align-items-center justify-content-center ">
            <img src={essential} />
          </div>
          <div className="col-md-6 mt-lg-0 mt-md-0 mt-sm-4 mt-3">
            <h2 className="mb-lg-4 mb-md-3 mb-sm-3 mb-3">
              Seamless Socializing, Unforgettable Experiences
            </h2>

            <div className="d-flex flex-column gap-xl-5 gap-lg-4 gap-md-3 gap-sm-3 gap-2">
            {items.map((val, index) => (
              <div className="d-flex gap-3">
                <FaCheck style={{ color: "#988265", fontSize: '24px', width: '60px' }} />
                <div className="d-flex flex-column">
                  <p className="mb-0">{val.title}</p>
                  <small className="mb-0">{val.description}</small>
                </div>
              </div>
            ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Essential;
