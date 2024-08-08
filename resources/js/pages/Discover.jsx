import React from "react";

import discover1 from "../assets/Images/discover-1.svg";
import discover2 from "../assets/Images/discover-2.svg";
import discover3 from "../assets/Images/discover-3.svg";
import discover4 from "../assets/Images/discover-4.svg";

import message from "../assets/Images/icons/message.svg";
import groupmessage from "../assets/Images/icons/groupmessage.svg";
import time from "../assets/Images/icons/time.svg";
import share from "../assets/Images/icons/share.svg";

import detail from "../assets/Images/icons/detail.svg";
import customize from "../assets/Images/icons/customize.svg";
import privacy from "../assets/Images/icons/privacy.svg";
import format from "../assets/Images/icons/format.svg";

import express from "../assets/Images/icons/express.svg";
import user from "../assets/Images/icons/user.svg";
import friend from "../assets/Images/icons/participant.svg";
import bookmark from "../assets/Images/icons/bookmark.svg";

import host from "../assets/Images/icons/host.svg";
import notify from "../assets/Images/icons/notify.svg";
import group from "../assets/Images/icons/group.svg";
import update from "../assets/Images/icons/update.svg";

function Discover() {
  const items = [
    {
      img: message,
      title: "Private Messaging:",
      description:
        "Send messages directly to users you've connected with, ensuring you stay in touch with friends old and new.",
    },
    {
      img: groupmessage,
      title: "Group Chat For Events:",
      description:
        "Engage with attendees of Mixxers you're part of or hosting. Your group chat is exclusively for those accepted to a specific Mixxer, ensuring a focused and relevant conversation.",
    },
    {
      img: time,
      title: "Comprehensive Chat History:",
      description:
        "Access the chat history of all past and current Mixxers you've attended, as well as private messaging history with your friends. This makes it easy to revisit important discussions and moments.",
    },
    {
      img: share,
      title: "Media Sharing:",
      description:
        "Elevate your conversations by uploading and sharing pictures within chats. Share photos from your Mixxers to highlight the fun moments and keep the excitement alive.",
    },
  ];

  const data = [
    {
      img: detail,
      title: "Add Detailed Information:",
      description:
        "Provide all the essential details for your Mixxer, including a captivating description, schedule, location, and photos to set the right tone.",
    },
    {
      img: customize,
      title: "Customization Options:",
      description:
        "Tailor your Mixxer to fit your desired audience perfectly. Set attendee limits, specify gender preferences, and apply age restrictions to ensure a curated experience.",
    },
    {
      img: privacy,
      title: "Privacy Settings:",
      description:
        "Decide how you want to share your Mixxer. Choose between public visibility, private (invite-only), or sharing exclusively with friends.",
    },
    {
      img: format,
      title: "Flexible Formats:",
      description:
        "Select whether your Mixxer will be in-person, virtual, or a hybrid of both. For virtual Mixxers, take advantage of integrated video chatting, allowing attendees to join via a link and participate seamlessly.",
    },
  ];

  const loop = [
    {
      img: host,
      title: "Host Notifications:",
      description:
        "Receive instant updates about who requests to join your Mixxers, helping you manage your gatherings effortlessly.",
    },
    {
      img: notify,
      title: "Attendee Notifications:",
      description:
        "Get timely alerts when you're accepted to a Mixxer, so you can plan and prepare for your upcoming social activities.",
    },
    {
      img: group,
      title: "Friend Requests:",
      description:
        "Stay connected by seeing and responding to friend requests directly from your notifications tab.",
    },
    {
      img: update,
      title: "Mixer Updates:",
      description:
        "Keep track of all your Mixxer-related activities with reminders for when a Mixxer is about to start, friendly check-ins during a Mixxer, feedback requests about your Mixxer experience, and reminders to upload photos of your Mixxer with your new friends.",
    },
  ];

  const social = [
    {
      img: express,
      title: "Express Yourself:",
      description:
        "Share your name, age, bio, and interests to let others know more about you and what you're passionate about.",
    },
    {
      img: user,
      title: "Friend Connections:",
      description:
        "View and manage your list of friends, making it easy to stay connected with those you care about.",
    },
    {
      img: friend,
      title: "Mixxer Participation:",
      description:
        "See the Mixxers you're currently hosting and attending. Track the number of Mixxers you've hosted or participated in, highlighting your social engagement.",
    },
    {
      img: bookmark,
      title: "Bookmarks:",
      description:
        "Keep track of your favorite Mixxers by bookmarking them for easy access and future reference.",
    },
  ];

  return (
    <div className="mt-5 disc-text">
      <div className="text-center mb-4">
        <h1 className="pb-3">Seamless Socializing with Mixxer</h1>
        <h5>
          Discover the ultimate platform for planning and enjoying small group
          outings with ease. Mixxer offers a suite of features designed to
          enhance your social experiences and keep you connected with friends
          and new acquaintances.
        </h5>
      </div>

      <div className="container">
        <div
          className="row"
          style={{ marginTop: "100px", marginBottom: "100px" }}
        >
          <div className="col-md-6 mb-5">
            <img src={discover1} alt="discover" style={{ width: "80%" }} />
          </div>
          <div className="col-md-6 d-flex flex-column align-items-center justify-content-center">
            <div className="d-flex flex-column gap-3">
              <h2>Stay Connected with Ease</h2>
              <p>
                Enhance your Mixxer experience with our intuitive chat feature
                designed to keep you connected with your social circles
                seamlessly. Whether you're planning your next outing or sharing
                memorable moments, our chat functionality offers everything you
                need.
              </p>
            </div>

            <div className="row mt-5">
              {items.map((val, index) => (
                <div className="col-md-6 mb-5">
                  <div className="d-flex gap-3">
                    <div className="d-flex flex-column gap-3">
                      <div className="d-flex align-items-center gap-2">
                        <img
                          src={val.img}
                          style={{ width: "30px", height: "30px" }}
                        />
                        <p className="mb-0">{val.title}</p>
                      </div>
                      <small>{val.description}</small>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        <div className="row">
          <div className="col-md-6 mb-5 order-md-2">
            <img
              src={discover2}
              style={{ width: "80%" }}
              alt="discover"
            />
          </div>

          <div className="col-md-6 d-flex flex-column align-items-center justify-content-center order-md-1">
            <div className="d-flex flex-column gap-3">
              <h2>Plan Your Perfect Mixxer</h2>
              <p>
                Unleash your creativity and plan the perfect Mixxer with our
                comprehensive creation feature. Whether you're organizing a
                casual get-together or a themed gathering, Mixxer offers all the
                tools you need to make it happen.
              </p>
            </div>

            <div className="row mt-5">
              {data.map((det, index) => (
                <div key={index} className="col-md-6 mb-5">
                  <div className="d-flex gap-3">
                    <div className="d-flex flex-column gap-3">
                      <div className="d-flex align-items-center gap-2">
                        <img
                          src={det.img}
                          style={{ width: "30px", height: "30px" }}
                          alt={det.title}
                        />
                        <p className="mb-0">{det.title}</p>
                      </div>
                      <small>{det.description}</small>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        <div className="row">
          <div className="col-md-6 mb-5">
            <img src={discover3} alt="discover" style={{ width: "80%" }} />
          </div>
          <div className="col-md-6 d-flex flex-column align-items-center justify-content-center">
            <div className="d-flex flex-column gap-3">
              <h2>Always in Loop</h2>
              <p>
                Stay in the loop with Mixxer’s tailored notification feature,
                designed to keep you informed and engaged with all your Mixxer
                activities. Whether you're a host or an attendee, our
                notifications ensure you never miss a beat.
              </p>
            </div>

            <div className="row mt-5">
              {loop.map((beat, index) => (
                <div className="col-md-6 mb-5">
                  <div className="d-flex gap-3">
                    <div className="d-flex flex-column gap-3">
                      <div className="d-flex align-items-center gap-2">
                        <img
                          src={beat.img}
                          style={{ width: "30px", height: "30px" }}
                        />
                        <p className="mb-0">{beat.title}</p>
                      </div>
                      <small>{beat.description}</small>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        <div className="row mb-4">
          <div className="col-md-6 d-flex flex-column align-items-center justify-content-center">
            <div className="d-flex flex-column gap-3">
              <h2>Your Social Hub</h2>
              <p>
                Your Mixxer profile is your personal space to showcase who you
                are and what you love. Connect with like-minded individuals and
                keep track of your social activities with these features.
              </p>
            </div>

            <div className="row mt-5">
              {social.map((hub, index) => (
                <div className="col-md-6 mb-5">
                  <div className="d-flex gap-3">
                    <div className="d-flex flex-column gap-3">
                      <div className="d-flex align-items-center gap-2">
                        <img
                          src={hub.img}
                          style={{ width: "30px", height: "30px" }}
                        />
                        <p className="mb-0">{hub.title}</p>
                      </div>
                      <small>{hub.description}</small>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
          <div className="col-md-6">
            <img src={discover4} alt="discover" style={{ width: "80%" }} />
          </div>
        </div>
      </div>
    </div>
  );
}

export default Discover;
