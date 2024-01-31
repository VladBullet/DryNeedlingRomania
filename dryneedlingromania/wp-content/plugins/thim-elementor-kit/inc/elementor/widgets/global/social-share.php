<?php

namespace Elementor;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_Social_Share extends Thim_Ekit_Widget_Social {

	public function get_name() {
		return 'thim-ekits-social-share';
	}

	public function get_title() {
		return esc_html__( 'Social Share', 'thim-elementor-kit' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-share';
	}

	public function get_categories() {
		return array( \Thim_EL_Kit\Elementor::CATEGORY );
	}

	public function get_keywords() {
		return [
			'thim',
			'social',
			'social-share'
		];
	}

	public function get_base() {
		return basename( __FILE__, '.php' );
	}

	protected function social_icon_link_type( $social_repeater ) {
		$social_repeater->add_control(
			'social_key',
			array(
				'label'   => esc_html__( 'Type', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => array(
					'facebook'  => esc_html__( 'Facebook', 'thim-elementor-kit' ),
					'twitter'   => esc_html__( 'Twitter', 'thim-elementor-kit' ),
					'linkedin'  => esc_html__( 'Linkedin', 'thim-elementor-kit' ),
					'pinterest' => esc_html__( 'Pinterest', 'thim-elementor-kit' ),
				),
			)
		);
	}

	function thim_ekit_social_value_default() {
		return array(
			array(
				'social_icon_icons'            => array(
					'value'   => 'fab fa-facebook-f',
					'library' => 'Font Awesome 5 Brands',
				),
				'social_icon_label'            => 'Facebook',
				'social_icon_icon_hover_color' => '#3b5998',
				'social_key'                   => 'facebook',
			),
			array(
				'social_icon_icons'            => array(
					'value'   => 'fab fa-twitter',
					'library' => 'Font Awesome 5 Brands',
				),
				'social_icon_label'            => 'Twitter',
				'social_icon_icon_hover_color' => '#1da1f2',
				'social_key'                   => 'twitter',
			),
			array(
				'social_icon_icons'            => array(
					'value'   => 'fab fa-linkedin-in',
					'library' => 'Font Awesome 5 Brands',
				),
				'social_icon_label'            => 'LinkedIn',
				'social_icon_icon_hover_color' => '#0077b5',
				'social_key'                   => 'linkedin',
			),
		);
	}

	protected function render_raw() {
		$settings = $this->get_settings();
		?>

		<ul class="thim-social-media">
			<?php foreach ( $settings['social_icon_add_icons'] as $key => $icon ) : ?>
				<?php
				if ( $icon['social_icon_icons'] != '' ) :

					switch ( $icon['social_key'] ) {
						case 'facebook':
							$link_share = 'https://www.facebook.com/sharer.php?u=' . urlencode( get_permalink() );
							break;
						case'twitter':
							$link_share = 'https://twitter.com/share?url=' . urlencode( get_permalink() ) . '&amp;text=' . rawurlencode( esc_attr( get_the_title() ) );
							break;
						case'pinterest':
							$link_share = 'http://pinterest.com/pin/create/button/?url=' . urlencode( get_permalink() ) . '&amp;description=' . rawurlencode( esc_attr( get_the_excerpt() ) ) . '&amp;media=' . urlencode( wp_get_attachment_url( get_post_thumbnail_id() ) ) . ' onclick="window.open(this.href); return false;"';
							break;
						case'linkedin':
							$link_share = 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode( get_permalink() ) . '&title=' . rawurlencode( esc_attr( get_the_title() ) ) . '&summary=&source=' . rawurlencode( esc_attr( get_the_excerpt() ) );
							break;
					}
					?>
					<li class="elementor-repeater-item-<?php echo esc_attr( $icon['_id'] ); ?>">
						<a target="_blank" href="<?php echo $link_share; ?>"
						   title="<?php echo esc_html( $icon['social_icon_label'] ); ?>">
							<?php if ( $settings['social_icon_style'] != 'text' && $settings['social_icon_style_icon_position'] == 'before' ) : ?>
								<?php Icons_Manager::render_icon( $icon['social_icon_icons'], array( 'aria-hidden' => 'true' ) ); ?>
							<?php endif; ?>

							<?php if ( $settings['social_icon_style'] != 'icon' ) : ?>
								<?php echo esc_html( $icon['social_icon_label'] ); ?>
							<?php endif; ?>

							<?php if ( $settings['social_icon_style'] != 'text' && $settings['social_icon_style_icon_position'] == 'after' ) : ?>
								<?php Icons_Manager::render_icon( $icon['social_icon_icons'], array( 'aria-hidden' => 'true' ) ); ?>
							<?php endif; ?>
						</a>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}
