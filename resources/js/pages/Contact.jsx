import React, { useState } from "react";

import contactimg from "../assets/Images/contact-img.png";
function Contact() {
  const [formData, setFormData] = useState({
    fullName: "",
    email: "",
    message: "",
  });

  const [errors, setErrors] = useState({
    fullName: "",
    email: "",
    message: "",
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
    setErrors({ ...errors, [name]: "" });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    let formValid = true;
    const newErrors = { ...errors };

    if (!formData.fullName.trim()) {
      newErrors.fullName = "Name must required";
      formValid = false;
    }

    if (!formData.email.trim()) {
      newErrors.email = "Email must required";
      formValid = false;
    } else if (!isValidEmail(formData.email)) {
      newErrors.email = "Invalid email format";
      formValid = false;
    }

    if (!formData.message.trim()) {
      newErrors.message = "Don't leave message empty";
      formValid = false;
    }

    if (formValid) {
      console.log("Submitted Data:", formData);
      setFormData({
        fullName: "",
        email: "",
        message: "",
      });
    } else {
      setErrors(newErrors);
    }
  };

  const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

  return (
    <div className="contact-bg">
      <div className="container">
        <div className="row">
          <div className="col-md-6 contact-info">
            <h1>Have a question or need any assistance?</h1>
            <p className="mb-5">
              We're here to help! Please fill out the form below, and we'll get
              back to you as soon as possible.
            </p>
            <form
              className="d-flex flex-column gap-4 mt-5"
              onSubmit={handleSubmit}
            >
              <div className="d-flex flex-column gap-1">
                <div className="d-flex justify-content-between">
                  <label>Full Name</label>
                  {errors.fullName && (
                    <span style={{ color: "red" }}>{errors.fullName}</span>
                  )}
                </div>
                <input
                  className="auth-input"
                  type="text"
                  name="fullName"
                  placeholder="Write name"
                  value={formData.fullName}
                  onChange={handleChange}
                />
              </div>

              <div className="d-flex flex-column gap-1">
                <div className="d-flex justify-content-between">
                  <label>Email Address</label>
                  {errors.email && (
                    <span style={{ color: "red" }}>{errors.email}</span>
                  )}
                </div>
                <input
                  className="auth-input"
                  type="text"
                  name="email"
                  placeholder="example@email.com"
                  value={formData.email}
                  onChange={handleChange}
                />
              </div>

              <div className="d-flex flex-column gap-1">
                <div className="d-flex justify-content-between">
                  <label>What do you want to discuss about?</label>
                  {errors.message && (
                    <span style={{ color: "red" }}>{errors.message}</span>
                  )}
                </div>
                <textarea
                  className="auth-input"
                  name="message"
                  placeholder="write here..."
                  value={formData.message}
                  onChange={handleChange}
                ></textarea>
              </div>

              <button type="submit">Contact us</button>
            </form>
          </div>

          <div className="col-md-6">
            <img
              src={contactimg}
              alt="CONTACTING"
              className="w-100 d-lg-block d-md-block d-sm-none d-none"
            />
          </div>
        </div>
      </div>
    </div>
  );
}

export default Contact;
